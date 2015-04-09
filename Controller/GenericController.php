<?php

namespace BlueBear\AdminBundle\Controller;

use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\AdminBundle\Admin\AdminFactory;
use BlueBear\BaseBundle\Behavior\ControllerTrait;
use Doctrine\ORM\Mapping\ClassMetadata;
use EE\DataExporterBundle\Service\DataExporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GenericController
 *
 * Generic CRUD controller
 */
class GenericController extends Controller
{
    use ControllerTrait;

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
        /** @var Admin $admin */
        $admin = $this
            ->get('bluebear.admin.factory')
            ->getAdminFromRequest($request);
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // set entities list
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
        $admin = $this
            ->get('bluebear.admin.factory')
            ->getAdminFromRequest($request);
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
                return $this->redirect($this->generateUrl($admin->generateRouteName('edit'), [
                    'id' => $admin->getEntity()->getId()
                ]));
            } else {
                // redirect to list
                return $this->redirect($this->generateUrl($admin->generateRouteName('list')));
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

        if ($form->isValid()) {
            // save entity
            $admin->saveEntity();
            // inform user everything went fine
            $this->setMessage('bluebear.admin.saved', 'info', [
                '%entity%' => $admin->getEntityLabel()
            ]);
            if ($request->request->get('submit') == 'save') {
                return $this->redirect($this->generateUrl($admin->generateRouteName('edit'), [
                    'id' => $admin->getEntity()->getId()
                ]));
            } else {
                // redirect to list
                return $this->redirect($this->generateUrl($admin->generateRouteName('list')));
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
            return $this->redirect($this->generateUrl($admin->generateRouteName('list')));
        }
        return [
            'admin' => $admin,
            'form' => $form->createView()
        ];
    }

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
        $association = $metadata->getAssociationMappings();
        $hooks = [];

        foreach ($fields as $fieldName) {
            $fieldMetadata = $metadata->getFieldMapping($fieldName);

            if ($fieldMetadata['type'] == 'simple_array' || $fieldMetadata['type'] == 'array') {
                //unset($exportColumns[$fieldName]);
            } else {
                $exportColumns[$fieldName] = $fieldName;
            }
        }
        foreach ($association as $fieldName => $mapping) {
            //$exportColumns[$fieldName . 'id'] = $fieldName;
        }
        //var_dump($exportColumns);
        $exporter
            ->setOptions($exportType, [
            'fileName' => '/home/afrezet/test.csv'
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
        // check permissions and actions
        $this->forward404Unless($admin->isActionGranted($admin->getCurrentAction()->getName(), $this->getUser()->getRoles()),
            'User not allowed for action "' . $admin->getCurrentAction()->getName() . '"');
    }

    /**
     * @return AdminFactory
     */
    protected function getAdminFactory()
    {
        return $this->getContainer()->get('bluebear.admin.factory');
    }
}
