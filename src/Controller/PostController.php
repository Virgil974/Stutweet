<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    #[Route('/', name:"home")]
    public function index(Request $request, PostRepository $repository): Response
    {
        $search  = $request->request->get("search"); //ici il va rechercher ce qui se trouve dans le poste (ou query si on avait laissé query)qui porte le nom search.
        $posts = $repository->findAll(); //SELECT*FROM`post`;
        if($search){
            $posts = $repository->findBySearch($search);//SELECT*FROM`post` WHERE title LIKE :search
        }
        return $this->render('post/index.html.twig', [
            "posts" => $posts
        ]);
    }


    #[Route('/post/new')]
    public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form ->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        //$uploads,
                        $this->getParameter('uploads'), 
                        $newFilename
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                //$post->setimagename($newFilename);
                $post->setImage($newFilename);
            }

            $post->setUser($this->getUser());
            $post->setPublishedAt(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('post/form.html.twig', [
            "form" => $form->createView()  
        ]);
    }

    #[Route('/post/edit/{id<\d+>}', name:"edit-post")]
    public function update(Request $request, Post $post, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $post->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas modifier une publication qui ne vous appartient pas.");
            return $this->redirectToRoute("home");
            //throw new AccessDeniedException("Vous n'avez pas accès à cette fonctionalité.");
        }
        $form = $this->createForm(PostType::class, $post);
        $form ->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$post->setPublishedAt(new \DateTime());
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("home");
            //dump($post);
        }
        return $this->render('post/form.html.twig', [
            #"post_form" => $form->createView()
            "form" => $form->createView()  
        ]);
    }

    #[Route('/post/delete/{id<\d+>}', name:"delete-post")]
    public function delete(Post $post, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $post->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas supprimer une publication qui ne vous appartient pas.");
            return $this->redirectToRoute("home");
            //throw new AccessDeniedException("Vous n'avez pas accès à cette fonctionalité.");
        }
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute("home");
    }

    #[Route('/post/copy/{id<\d+>}', name:"copy-post")]
    public function duplicate(Post $post, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $post->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas dupliquer une publication qui ne vous appartient pas.");
            return $this->redirectToRoute("home");
            //throw new AccessDeniedException("Vous n'avez pas accès à cette fonctionalité.");
        }
        $copyPost = clone $post;
        $em = $doctrine->getManager();
        $em->persist($copyPost);
        $em->flush();
        return $this->redirectToRoute("home");
    }

    /*#[Route('/post/search/{search}', name:"search-post")]
    public function search(string $search): Response
    {
        dump($search);
        return new Response("");
        //return $this->redirectToRoute("home");
    }*/
}



