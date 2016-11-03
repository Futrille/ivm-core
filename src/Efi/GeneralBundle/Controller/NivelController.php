<?php

namespace Efi\GeneralBundle\Controller;

use AppBundle\GeneralResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Efi\GeneralBundle\Entity\Nivel;
use Efi\GeneralBundle\Form\NivelType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nivel controller.
 *
 * @Route("/nivel")
 */
class NivelController extends Controller
{
    /**
     * Listado de Familias. Filtradas por Pareja Ministerial (Codigo).
     * Adem&aacute;s muestra los integrantes en cada una de ellas.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Listado resumen de Familias conectadas."
     * )
     */
    public function indexAction(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//        $nivels = $em->getRepository('EfiGeneralBundle:Nivel')->findAll();
//
//        return $this->render('nivel/index.html.twig', array(
//            'nivels' => $nivels,
//        ));

        $em = $this->getDoctrine()->getManager();
        $result = array();
        $nivels = $em->getRepository('EfiGeneralBundle:Nivel')->findBy(array("padre" => null));
        foreach ($nivels as &$valor) {
            array_push($result, $valor);

            $subNivels = $em->getRepository('EfiGeneralBundle:Nivel')->findBy(array("padre" => $valor->getId()),array('orden' => 'ASC'));
            foreach ($subNivels as &$subValor) {
                array_push($result, $subValor);
            }
        }

        $response = new GeneralResponse();
        $codigo = $request->get('codigoPmi');


        $response->setData($result);
        $response->addToMetaData('codigo', $codigo);

        return $response->toJSON();
    }

    /**
     * Creates a new Nivel entity.
     *
     * @Route("/new", name="nivel_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $nivel = new Nivel();
        $form = $this->createForm('Efi\GeneralBundle\Form\NivelType', $nivel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nivel);
            $em->flush();

            return $this->redirectToRoute('nivel_show', array('id' => $nivel->getId()));
        }

        return $this->render('nivel/new.html.twig', array(
            'nivel' => $nivel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Nivel entity.
     *
     * @Route("/{id}", name="nivel_show")
     * @Method("GET")
     */
    public function showAction(Nivel $nivel)
    {
        $deleteForm = $this->createDeleteForm($nivel);

        return $this->render('nivel/show.html.twig', array(
            'nivel' => $nivel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Nivel entity.
     *
     * @Route("/{id}/edit", name="nivel_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Nivel $nivel)
    {
        $deleteForm = $this->createDeleteForm($nivel);
        $editForm = $this->createForm('Efi\GeneralBundle\Form\NivelType', $nivel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nivel);
            $em->flush();

            return $this->redirectToRoute('nivel_edit', array('id' => $nivel->getId()));
        }

        return $this->render('nivel/edit.html.twig', array(
            'nivel' => $nivel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Nivel entity.
     *
     * @Route("/{id}", name="nivel_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Nivel $nivel)
    {
        $form = $this->createDeleteForm($nivel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nivel);
            $em->flush();
        }

        return $this->redirectToRoute('nivel_index');
    }

    /**
     * Creates a form to delete a Nivel entity.
     *
     * @param Nivel $nivel The Nivel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Nivel $nivel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nivel_delete', array('id' => $nivel->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}