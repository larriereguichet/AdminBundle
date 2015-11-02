<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Form\Type\AdminListType;
use LAG\AdminBundle\Utils\RecursiveImplode;
use BlueBear\BaseBundle\Behavior\ControllerTrait;
use DateTime;
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
 * Class GenericController.
 *
 * Generic CRUD controller
 */
class GenericController extends Controller
{
    use ControllerTrait, RecursiveImplode;

    protected $entityClass;

    protected $formType;

    /**
     * Generic list action.
     *
     * @Template("LAGAdminBundle:Generic:list.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        // retrieve admin from request route parameters
        $admin = $this->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // creating form
        $form = $this->createForm(new AdminListType(), [
            'entities' => $admin->getEntities()
        ], [
            'batch_actions' => $admin->getCurrentAction()->getBatchActions(),
        ]);
        $form->handleRequest($request);

        if ($request->get('export')) {
            return $this->exportEntities($admin, $request->get('export'));
        }
        if ($form->isValid()) {
            var_dump($form->getData());
            die('lol');
        }

        return [
            'admin' => $admin,
            'action' => $admin->getCurrentAction(),
            'form' => $form->createView()
        ];
    }

    /**
     * Generic create action.
     *
     * @Template("LAGAdminBundle:Generic:edit.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        /** @var Admin $admin */
        $admin = $this->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // create form
        $form = $this->createForm($admin->getFormType(), $admin->getEntity());
        $form->handleRequest($request);

        if ($form->isValid()) {
            // save entity
            $admin->save();

            if ($request->request->get('submit') == 'save') {
                // if save is pressed, user stay on the edit view
                $editRoute = $this
                    ->get('lag.admin.routing')
                    ->generateRouteName('edit', $admin);

                return $this->redirectToRoute($editRoute, [
                    'id' => $admin->getEntity()->getId(),
                ]);
            } else {
                // otherwise user is redirected to list view
                $listRoute = $this
                    ->get('lag.admin.routing')
                    ->generateRouteName('list', $admin);

                return $this->redirect($this->generateUrl($listRoute));
            }
        }

        return [
            'admin' => $admin,
            'form' => $form->createView(),
        ];
    }

    /**
     * Generic edit action.
     *
     * @Template("LAGAdminBundle:Generic:edit.html.twig")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function editAction(Request $request)
    {
        $admin = $this
            ->get('lag.admin.factory')
            ->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // create form
        $form = $this->createForm($admin->getFormType(), $admin->getEntity());
        $form->handleRequest($request);
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($form->isValid()) {
            $admin->save();

            if ($request->request->get('submit') == 'save') {
                $saveRoute = $this
                    ->get('lag.admin.routing')
                    ->generateRouteName('edit', $admin);

                return $this->redirectToRoute($saveRoute, [
                    'id' => $accessor->getValue($admin->getEntity(), 'id'),
                ]);
            } else {
                $listRoute = $this
                    ->get('lag.admin.routing')
                    ->generateRouteName('list', $admin);
                // redirect to list
                return $this->redirectToRoute($listRoute);
            }
        }

        return [
            'admin' => $admin,
            'form' => $form->createView(),
        ];
    }

    /**
     * Generic delete action.
     *
     * @Template("LAGAdminBundle:Generic:delete.html.twig")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $admin = $this
            ->get('lag.admin.factory')
            ->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // create form to avoid deletion by url
        $form = $this->createForm('delete', $admin->getEntity());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $admin->delete();
            // redirect to list
            $listRoute = $this
                ->get('lag.admin.routing')
                ->generateRouteName('list', $admin);

            return $this->redirectToRoute($listRoute);
        }

        return [
            'admin' => $admin,
            'form' => $form->createView(),
        ];
    }

    /**
     * Export entities according to a type (json, csv, xls...).
     *
     * @param AdminInterface $admin
     * @param $exportType
     *
     * @return Response
     *
     * @throws MappingException
     */
    protected function exportEntities(AdminInterface $admin, $exportType)
    {
        // check allowed export types
        $this->forward404Unless(in_array($exportType, ['json', 'html', 'xls', 'csv', 'xml']));
        /** @var DataExporter $exporter */
        $exporter = $this->get('ee.dataexporter');
        $metadata = $this
            ->getEntityManager()
            ->getClassMetadata($admin->getRepository()->getClassName());
        $exportColumns = [];
        $fields = $metadata->getFieldNames();
        $hooks = [];

        foreach ($fields as $fieldName) {
            $exporter->addHook(function ($fieldValue) {
                // if field is an array
                if (is_array($fieldValue)) {
                    $value = $this->recursiveImplode(', ', $fieldValue);
                } elseif ($fieldValue instanceof DateTime) {
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
                'fileName' => $admin->getName() . '-export-' . date('Y-m-d'),
            ])
            ->setColumns($exportColumns)
            ->setData($admin->getEntities());
        foreach ($hooks as $hookName => $hook) {
            $exporter->addHook($hook, $hookName);
        }

        return $exporter->render();
    }

    /**
     * Forward to 404 if user is not allowed by configuration for an action.
     *
     * @param AdminInterface $admin
     */
    protected function forward404IfNotAllowed(AdminInterface $admin)
    {
        // TODO move authorizations logic into kernel.request event
        $this->forward404Unless($this->getUser(), 'You must be logged to access to this url');
        // check permissions and actions
        $this->forward404Unless(
            $admin->isActionGranted($admin->getCurrentAction()->getName(), $this->getUser()->getRoles()),
            sprintf('User not allowed for action "%s"', $admin->getCurrentAction()->getName())
        );
    }

    /**
     * Return an Admin object according to the request route parameters.
     *
     * @param Request $request
     *
     * @return AdminInterface
     *
     * @throws Exception
     */
    protected function getAdminFromRequest(Request $request)
    {
        return $this
            ->get('lag.admin.factory')
            ->getAdminFromRequest($request);
    }
}
