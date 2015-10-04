<?php

namespace BlueBear\AdminBundle\Controller;

use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\AdminBundle\Routing\RoutingLoader;
use BlueBear\AdminBundle\Utils\RecursiveImplode;
use BlueBear\BaseBundle\Behavior\ControllerTrait;
use DateTime;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use EE\DataExporterBundle\Service\DataExporter;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class GenericController
 *
 * Generic CRUD controller
 */
class GenericController extends Controller
{
    use ControllerTrait, RecursiveImplode;

    protected $entityClass;

    protected $formType;

    /**
     * Generic list action
     *
     * @Template()
     * @param Request $request
     * @return array
     */
    public function listAction(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // find entities list
        $admin->findEntities($request->get('page', 1), $request->get('sort', null), $request->get('order', 'ASC'));

        if ($request->get('export', false)) {
            return $this->exportEntities($admin, $request->get('export'));
        }
        return [
            'admin' => $admin,
            'action' => $admin->getCurrentAction()
        ];
    }

    /**
     * Generic create action
     *
     * @Template("BlueBearAdminBundle:Generic:edit.html.twig")
     * @param Request $request
     * @return array
     */
    public function createAction(Request $request)
    {
        /** @var Admin $admin */
        $admin = $this->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // create entity
        $entity = $admin->createEntity();
        // create form
        $form = $this->createForm($admin->getFormType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // save entity
            $admin->saveEntity();
            // inform user everything went fine
            $this->setMessage('bluebear.admin.' . $admin->getName() . '.saved');

            if ($request->request->get('submit') == 'save') {
                // if save is pressed, user stay on the edit view
                $route = $this
                    ->getRoutingLoader()
                    ->generateRouteName('edit', $admin);

                return $this->redirectToRoute($route, [
                    'id' => $admin->getEntity()->getId()
                ]);
            } else {
                // otherwise user is redirected to list view
                return $this->redirect($this->generateUrl($this->container->get('bluebear.admin.routing')->generateRouteName('list', $admin)));
            }
        }
        return [
            'admin' => $admin,
            'form' => $form->createView()
        ];
    }

    /**
     * @Template("BlueBearAdminBundle:Generic:edit.html.twig")
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function editAction(Request $request)
    {
        /** @var Admin $admin */
        $admin = $this->get('bluebear.admin.factory')->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // find entity
        $admin->findEntity('id', $request->get('id'));
        // create form
        $form = $this->createForm($admin->getFormType(), $admin->getEntity());
        $form->handleRequest($request);
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($form->isValid()) {
            // save entity
            $admin->saveEntity();
            // inform user everything went fine
            $this->setMessage('bluebear.admin.saved', 'info', [
                '%entity%' => $admin->getEntityLabel()
            ]);
            if ($request->request->get('submit') == 'save') {
                return $this->redirect($this->generateUrl($this->container->get('bluebear.admin.routing')->generateRouteName('edit', $admin), [
                    'id' => $accessor->getValue($admin->getEntity(), 'id')
                ]));
            } else {
                // redirect to list
                return $this->redirect($this->generateUrl($this->container->get('bluebear.admin.routing')->generateRouteName('list', $admin)));
            }
        }
        return [
            'admin' => $admin,
            'form' => $form->createView()
        ];
    }

    /**
     * Generic delete action
     *
     * @Template("BlueBearAdminBundle:Generic:delete.html.twig")
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        /** @var Admin $admin */
        $admin = $this->get('bluebear.admin.factory')->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // find entity
        $admin->findEntity('id', $request->get('id'));
        // create form to avoid deletion by url
        $form = $this->createForm('delete', $admin->getEntity());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $admin->deleteEntity();
            // inform user everything went fine
            $this->setMessage('bluebear.admin.deleted', 'info', [
                '%entity%' => $admin->getEntityLabel()
            ]);
            // redirect to list
            return $this->redirect($this->generateUrl($this->container->get('bluebear.admin.routing')->generateRouteName('list', $admin)));
        }
        return [
            'admin' => $admin,
            'form' => $form->createView()
        ];
    }

    /**
     * Export entities according to a type (json, csv, xls...)
     *
     * @param Admin $admin
     * @param $exportType
     * @return Response
     * @throws MappingException
     */
    protected function exportEntities(Admin $admin, $exportType)
    {
        // check allowed export types
        $this->forward404Unless(in_array($exportType, ['json', 'html', 'xls', 'csv', 'xml']));
        /** @var DataExporter $exporter */
        $exporter = $this->get('ee.dataexporter');

        /** @var ClassMetadata $metadata */
        $metadata = $this->getEntityManager()->getClassMetadata($admin->getRepository()->getClassName());
        $exportColumns = [];
        $fields = $metadata->getFieldNames();
        $hooks = [];

        foreach ($fields as $fieldName) {
            $exporter->addHook(function ($fieldValue) {
                // if field is an array
                if (is_array($fieldValue)) {
                    $value = $this->recursiveImplode(', ', $fieldValue);
                } else if ($fieldValue instanceof DateTime) {
                    // format date in string
                    $value = $fieldValue->format('c');
                } else {
                    $value = $fieldValue;
                }
                return $value;
            }, "{$fieldName}");
            // add field column to export
            $exportColumns[$fieldName] = $fieldName;
        }
        $exporter
            ->setOptions($exportType, [
                'fileName' => $admin->getName() . '-export-' . date('Y-m-d')
            ])
            ->setColumns($exportColumns)
            ->setData($admin->getEntities());
        foreach ($hooks as $hookName => $hook) {
            $exporter->addHook($hook, $hookName);
        }
        return $exporter->render();
    }

    /**
     * Forward to 404 if user is not allowed by configuration for an action
     *
     * @param Admin $admin
     */
    protected function forward404IfNotAllowed(Admin $admin)
    {
        $this->forward404Unless($this->getUser(), 'You must be logged to access to this url');
        // check permissions and actions
        $this->forward404Unless(
            $admin->isActionGranted($admin->getCurrentAction()->getName(), $this->getUser()->getRoles()),
            sprintf('User not allowed for action "%s"', $admin->getCurrentAction()->getName())
        );
    }

    /**
     * Return an Admin object according to the request route parameters
     *
     * @param Request $request
     * @return Admin
     * @throws Exception
     */
    protected function getAdminFromRequest(Request $request)
    {
        return $this
            ->get('bluebear.admin.factory')
            ->getAdminFromRequest($request);
    }

    /**
     * Return admin routing loader
     *
     * @return RoutingLoader
     */
    protected function getRoutingLoader()
    {
        return $this
            ->container
            ->get('bluebear.admin.routing');
    }
}
