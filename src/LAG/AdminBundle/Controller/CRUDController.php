<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Form\Handler\ListFormHandler;
use LAG\AdminBundle\Form\Type\AdminListType;
use LAG\AdminBundle\Form\Type\BatchActionType;
use Exception;
use LAG\AdminBundle\Form\Type\DeleteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Class CRUDController
 *
 * Generic CRUD controller
 */
class CRUDController extends Controller
{
    /**
     * Generic list action.
     *
     * @Template("LAGAdminBundle:CRUD:list.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function listAction(Request $request)
    {
        // retrieve admin from request route parameters
        $admin = $this->getAdminFromRequest($request);
        $admin->handleRequest($request, $this->getUser());

        // retrieve batch actions from configuration
        $batchActions = $admin
            ->getCurrentAction()
            ->getConfiguration()
            ->getParameter('batch');

        // pass admin and current action to the view
        $viewParameters = [
            'admin' => $admin,
            'action' => $admin->getCurrentAction(),
        ];

        // if batch are configured, we handle a list of checkboxes
        if ($batchActions) {
            // creating list form
            $form = $this->createForm(AdminListType::class, [
                'entities' => $admin->getEntities()
            ], [
                'batch_actions' => $batchActions['items'],
                'admin' => $admin
            ]);
            $form->handleRequest($request);

            if ($form->isValid()) {
                // get ids and batch action from list form data
                $formHandler = new ListFormHandler();
                $data = $formHandler->handle($form);

                if (count($data['ids'])) {
                    $batchForm = $this->createForm(BatchActionType::class, [
                        'batch_action' => $data['batch_action'],
                        'entity_ids' => $data['ids']
                    ], [
                        'labels' => $data['labels']
                    ]);

                    // render batch view
                    return $this->render('LAGAdminBundle:CRUD:batch.html.twig', [
                        'admin' => $admin,
                        'form' => $batchForm->createView()
                    ]);
                } else {
                    $this
                        ->addFlash('info', 'lag.admin.batch_no_entities');
                }

            }
            $viewParameters['form'] = $form->createView();
        }

        return $viewParameters;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function batchAction(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        $admin->handleRequest($request, $this->getUser());
        // create batch action form
        $form = $this->createForm(BatchActionType::class, [
            'batch_action' => [],
            'entity_ids' => []
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $admin->load([
                'id' => $data['entity_ids']
            ]);

            if ($data['batch_action'] == 'delete') {
                $admin->remove();
            }
        } else {
            throw new NotFoundHttpException('Invalid batch parameters : '.$form->getErrors(true, false));
        }
        // redirect to list view
        return $this->redirectToRoute($admin->generateRouteName('list'));
    }

    /**
     * Generic create action.
     *
     * @Template("LAGAdminBundle:CRUD:edit.html.twig")
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        $admin->handleRequest($request, $this->getUser());
        
        // check permissions
        $this->forward404IfNotAllowed($admin);
        
        // create form
        $form = $this->createForm($admin->getConfiguration()->getParameter('form'), $admin->create());
        $form->handleRequest($request);

        if ($form->isValid()) {
            // save entity
            $success = $admin->save();

            if ($success) {
                // if save is pressed, user stay on the edit view
                if ($request->request->get('submit') == 'save') {
                    $editRoute = $admin->generateRouteName('edit');

                    return $this->redirectToRoute($editRoute, [
                        'id' => $admin->getUniqueEntity()->getId(),
                    ]);
                } else {
                    // otherwise user is redirected to list view
                    $listRoute = $admin->generateRouteName('list');

                    return $this->redirectToRoute($listRoute);
                }
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
     * @Template("LAGAdminBundle:CRUD:edit.html.twig")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function editAction(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        $admin->handleRequest($request, $this->getUser());
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // create form
        $form = $this->createForm($admin->getConfiguration()->getParameter('form'), $admin->getUniqueEntity());
        $form->handleRequest($request);
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($form->isValid()) {
            $admin->save();

            if ($request->request->get('submit') == 'save') {
                $saveRoute = $admin->generateRouteName('edit');

                return $this->redirectToRoute($saveRoute, [
                    'id' => $accessor->getValue($admin->getUniqueEntity(), 'id'),
                ]);
            } else {
                $listRoute = $admin->generateRouteName('list');
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
     * @Template("LAGAdminBundle:CRUD:delete.html.twig")
     * @param Request $request
     * @return RedirectResponse|array
     */
    public function deleteAction(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        $admin->handleRequest($request, $this->getUser());
        // check permissions
        $this->forward404IfNotAllowed($admin);
        // create form to avoid deletion by url
        $form = $this->createForm(DeleteType::class, $admin->getUniqueEntity());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $admin->remove();
            // redirect to list
            $listRoute = $admin->generateRouteName('list');

            return $this->redirectToRoute($listRoute);
        }

        return [
            'admin' => $admin,
            'form' => $form->createView(),
        ];
    }

    /**
     * Throw a 404 Exception if $boolean is false or null
     *
     * @param $boolean
     * @param string $message
     */
    protected function forward404Unless($boolean, $message = '404 Not Found')
    {
        if (!$boolean) {
            throw $this->createNotFoundException($message);
        }
    }

    /**
     * Forward to 404 if user is not allowed by configuration for an action.
     *
     * @param AdminInterface $admin
     */
    protected function forward404IfNotAllowed(AdminInterface $admin)
    {
        $this->forward404Unless($this->getUser(), 'You must be logged to access to this url');
        $roles = $this
            ->getUser()
            ->getRoles();
        // check permissions and actions
        $this->forward404Unless(
            $admin->isActionGranted($admin->getCurrentAction()->getName(), $roles),
            sprintf('User with roles %s not allowed for action "%s"',
                implode(', ', array_map(function(Role $role) {
                    return $role->getRole();
                }, $roles)),
                $admin->getCurrentAction()->getName()
            )
        );
    }

    /**
     * Return an Admin object according to the request route parameters.
     *
     * @param Request $request
     * @return AdminInterface
     * @throws Exception
     */
    protected function getAdminFromRequest(Request $request)
    {
        return $this
            ->get('lag.admin.request_handler')
            ->handle($request);
    }
}