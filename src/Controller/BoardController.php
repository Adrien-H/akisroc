<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Post;
use App\Entity\Protagonist;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\TopicType;
use App\Security\Voter\BoardVoter;
use App\Utils\BBPlus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/board")
 */
class BoardController extends Controller
{
    /**
     * @Route("/{id}", name="board.index", requirements={"id"="\d+"})
     *
     * @param Board $board
     * @return Response
     */
    public function index(Board $board): Response
    {
        $d = $this->getDoctrine();
        $topicRepo = $d->getRepository(Topic::class);
        $topics = $topicRepo->findBy(['board' => $board], ['id' => 'DESC']);

        return $this->render('board/index.html.twig', [
            'board' => $board,
            'topics' => $topics
        ]);
    }

    /**
     * @Route("/{id}/add-topic", name="board.add-topic", requirements={"id"="\d+"})
     *
     *
     * @param Board $board
     * @param Request $request
     * @return Response
     */
    public function addTopic(Board $board, Request $request): Response
    {
        $this->denyAccessUnlessGranted(BoardVoter::ADD_TOPIC, $board);

        $topic = new Topic();
        $topic->setBoard($board);
        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $topic = $form->getData();
            $post = $form->get('post')->getData();
            $post->setUser($this->getUser());

            // @Todo Behaviour
            $now = new \DateTime();
            $post->setCreatedAt($now);
            $post->setUpdatedAt($now);

            $topic->addPost($post);
            $em->persist($post);
            $em->persist($topic);
            $em->flush();

            return $this->redirectToRoute('topic.index', ['id' => $topic->getId()]);
        }

        return $this->render('board/add-topic.html.twig', [
            'form' => $form->createView()
        ]);
    }
}