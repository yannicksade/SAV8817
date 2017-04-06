<?php
//
//namespace APM\UserBundle\Controller;
//
//use APM\UserBundle\Entity\Admin;
//use APM\UserBundle\Factory\TradeFactory;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Request;
//
///**
// * Admin controller.
// *
// */
//class AdminController extends Controller
//{
//    /**
//     * Lists all Admin entities.
//     *
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $admins = $em->getRepository('APMUserBundle:Admin')->findAll();
//
//        return $this->render('APMUserBundle:admin:index.html.twig', array(
//            'admins' => $admins,
//        ));
//    }
//
//    /**
//     * Creates a new Admin entity.
//     *
//     */
//    public function newAction(Request $request)
//    {
//        $admin = TradeFactory::getTradeProvider("admin");
//        $form = $this->createForm('APM\UserBundle\Form\AdminType', $admin);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($admin);
//            $em->flush();
//
//            return $this->redirectToRoute('apm_user_admin_show', array('id' => $admin->getId()));
//        }
//
//        return $this->render('APMUserBundle:admin:new.html.twig', array(
//            'admin' => $admin,
//            'form' => $form->createView(),
//        ));
//    }
//
//    /**
//     * Finds and displays a Admin entity.
//     *
//     */
//    public function showAction(Admin $admin)
//    {
//        $deleteForm = $this->createDeleteForm($admin);
//
//        return $this->render('APMUserBundle:admin:show.html.twig', array(
//            'admin' => $admin,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Creates a form to delete a Admin entity.
//     *
//     * @param Admin $admin The Admin entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm(Admin $admin)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('apm_user_admin_delete', array('id' => $admin->getId())))
//            ->setMethod('DELETE')
//            ->getForm();
//    }
//
//    /**
//     * Displays a form to edit an existing Admin entity.
//     *
//     */
//    public function editAction(Request $request, Admin $admin)
//    {
//        $deleteForm = $this->createDeleteForm($admin);
//        $editForm = $this->createForm('APM\UserBundle\Form\AdminType', $admin);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($admin);
//            $em->flush();
//
//            return $this->redirectToRoute('apm_user_admin_edit', array('id' => $admin->getId()));
//        }
//
//        return $this->render('APMUserBundle:admin:edit.html.twig', array(
//            'admin' => $admin,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Deletes a Admin entity.
//     *
//     */
//    public function deleteAction(Request $request, Admin $admin)
//    {
//        $form = $this->createDeleteForm($admin);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($admin);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('apm_user_admin_index');
//    }
//}
