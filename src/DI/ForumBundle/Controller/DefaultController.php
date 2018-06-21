<?php

namespace DI\ForumBundle\Controller;

use DI\ForumBundle\Entity\Answer;
use DI\ForumBundle\Entity\Subject;
use DI\ForumBundle\Form\AnswerType;
use DI\ForumBundle\Form\SubjectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listesubjects = $em->getRepository("DIForumBundle:Subject")->findAll();
        return $this->render('DIForumBundle:Default:index.html.twig',
            array('listesubjects' => $listesubjects));
    }

    public function addSubjectAction(Request $request)
    {

        $subject = new Subject();

        $form = $this->get('form.factory')->create(SubjectType::class, $subject);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($subject);
                $em->flush();
                $this->addFlash('success', "sujet créé avec succes");
                return $this->redirectToRoute('di_forum_homepage');
            }
        }
        return $this->render('DIForumBundle:Default:add.html.twig',
            array('formulaire' => $form->createView()));
    }


    public function viewSubjectAction(Subject $subject , Request $request)
    {

        $answer = new Answer();
        $form = $this->get('form.factory')->create(AnswerType::class, $answer);
        $em = $this->getDoctrine()->getManager();


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $answer->setSubject($subject);
                $em->persist($answer);
                $em->flush();

                $this->addFlash('success', "answer added");
                return $this->redirectToRoute('di_forum_viewsubject', array('id' => $subject->getId()));

            }
        }
        $answers_list =$em->getRepository("DIForumBundle:Answer")->findBy(
            array('subject' => $subject),
            null,null,null
        );

        return $this->render('DIForumBundle:Default:viewsubject.html.twig',
            array('answers_list' => $answers_list,'subject'=>$subject, 'form_answer' => $form->createView()));
    }

    public function deleteAnswerAction(Answer $answer){
        $em = $this->getDoctrine()->getManager();
        $em->remove($answer);
        $em->flush();
        $this->addFlash('success', "answer deleted with succes");
        return $this->redirectToRoute('di_forum_viewsubject', array('id' => $answer->getSubject()->getId()));

    }










}