<?php

namespace hugocorp\contactBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use hugocorp\contactBundle\Entity\Contact;
use hugocorp\contactBundle\Form\ContactType;
use hugocorp\contactBundle\Entity\Fichier;
use hugocorp\contactBundle\Form\FichierType;


class ContactController extends Controller
{
  /**
  * Lists all Contact entities.
  *
  * @Route("/", name="contact_index")
  * @Method("GET")
  */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $contacts = $em->getRepository('hugocorpcontactBundle:Contact')->findAll();

    return $this->render('contact/index.html.twig', array(
      'contacts' => $contacts,
    ));
  }

  /**
  * Creates a new Contact entity.
  *
  * @Route("/new", name="contact_new")
  * @Method({"GET", "POST"})
  */
  public function newAction(Request $request)
  {
    $contact = new Contact();
    $form = $this->createForm('hugocorp\contactBundle\Form\ContactType', $contact);
    $form->handleRequest($request);

    $contact->setDate(new \DateTime('now'));

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($contact);
      $em->flush();

      return $this->redirectToRoute('contact_show', array('id' => $contact->getId()));
    }

    return $this->render('contact/new.html.twig', array(
      'contact' => $contact,
      'form' => $form->createView(),
    ));
  }

  /**
  * Finds and displays a Contact entity.
  *
  * @Route("/{id}", name="contact_show")
  * @Method("GET")
  */
  public function showAction(Contact $contact)
  {
    $deleteForm = $this->createDeleteForm($contact);

    $contact ->getId();
    $em = $this->getDoctrine()->getManager();

    $fichiers = $em->getRepository('hugocorpcontactBundle:Fichier')->findBy(['contact'=>$contact]);

    return $this->render('contact/show.html.twig', array(
      'contact' => $contact,
      'delete_form' => $deleteForm->createView(),
      'fichiers' => $fichiers,
    ));
  }

  /**
  * Displays a form to edit an existing Contact entity.
  *
  * @Route("/{id}/edit", name="contact_edit")
  * @Method({"GET", "POST"})
  */
  public function editAction(Request $request, Contact $contact)
  {
    $deleteForm = $this->createDeleteForm($contact);
    $editForm = $this->createForm('hugocorp\contactBundle\Form\ContactType', $contact);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($contact);
      $em->flush();

      return $this->redirectToRoute('contact_show', array('id' => $contact->getId()));
    }

    return $this->render('contact/edit.html.twig', array(
      'contact' => $contact,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }


  /**
  * Add a file
  *
  * @Route("/{id}/fichier", name="contact_fichier")
  * @Method({"GET", "POST"})
  */
  public function fichierAction(Request $request, Contact $contact)
  {
    $fichier = new Fichier();
    $form = $this->createForm('hugocorp\contactBundle\Form\FichierType', $fichier);
    $form->handleRequest($request);

    $contact ->getId();
    $fichier->setContact($contact);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($fichier);
      $em->flush();

      return $this->redirectToRoute('contact_show', array('id' => $contact->getId()));
    }

    return $this->render('contact/fichier.html.twig', array(
      'fichier' => $fichier,
      'form' => $form->createView(),
    ));
  }


  /**
  * Deletes a Contact entity.
  *
  * @Route("/{id}", name="contact_delete")
  * @Method("DELETE")
  */
  public function deleteAction(Request $request, Contact $contact)
  {
    $form = $this->createDeleteForm($contact);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($contact);
      $em->flush();
    }

    return $this->redirectToRoute('contact_index');
  }

  /**
  * Creates a form to delete a Contact entity.
  *
  * @param Contact $contact The Contact entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createDeleteForm(Contact $contact)
  {
    return $this->createFormBuilder()
    ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
    ->setMethod('DELETE')
    ->getForm()
    ;
  }
}
