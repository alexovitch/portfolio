<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;

/**
 * Article controller.
 *
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * Lists all Article entities.
     *
     * @Route("/", name="article_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

//        $articles = $em->getRepository('AppBundle:Article')->findAll();
        $categories = $em->getRepository('AppBundle:Categorie')->findAll();

        $articlesManager =  $this->getDoctrine()->getManager()->getRepository('AppBundle:Article');
        $articles = $articlesManager->getAllArticles();


        return $this->render('article/index.html.twig', array(
            'articles' => $articles,
            'categories' => $categories,
        ));
    }

    /**
     * Lists all Article entities.
     *
     * @Route("/liste/{page}", name="article_list", defaults={"page"=1}, requirements={"page":"\d+"})
     * @param Request $request
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Method("GET")
     */
    public function listAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $nbArticlePages = 2;
        $categories = $em->getRepository('AppBundle:Categorie')->findAll();

        $articlesManager =  $this->getDoctrine()->getManager()->getRepository('AppBundle:Article');
        $articles = $articlesManager->getArticlesPages($nbArticlePages,$page);

        $nbPages = ceil(count($articles)/$nbArticlePages);

        return $this->render('article/list.html.twig', array(
            'articles' => $articles,
            'categories' => $categories,
            'nbPages' => $nbPages
        ));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/ajouter", name="article_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm('AppBundle\Form\ArticleType', $article);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $imageUploaded = $article->getImage();
            if ($imageUploaded) {
                $imageUploaded->upload();
                $post = $request->request->get('article');

                if (isset($post['mini']) && $post['mini']=='1') {

                    //$webPath = $this->get('kernel')->getRootDir().'/../web';

                    $url = $article->getImage()->getUrl();
                    $fileComplete = __DIR__.'/../../../web/image/'.$url;
                    /** @var  $miniImageService */
                    $miniImageService = $this->container->get('AppBundle.mini_image');
                    $miniImageService->createMini($fileComplete);
                }
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $session = $this->get('Session');
            $session->getFlashBag()->add('info','L\'article a bien été créé');
            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }

        return $this->render('article/new.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Article $article)
    {
        $deleteForm = $this->createDeleteForm($article);

        return $this->render('article/show.html.twig', array(
            'article' => $article,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/modifier", name="article_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Article $article)
    {
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
        $articleHasMini = $article->isMini();
        $imageOld = $article->getImage();
        $urlOld = ($imageOld)?$imageOld->getUrl():'';

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if ($article->getImage()) {
                $article->getImage()->upload();
                $imageNew = $article->getImage();
                $url = $imageNew->getUrl();
                $imgMin = __DIR__.'/../../../web/mini/mini-'.$url;
            }
            $post = $request->request->get('article');


            // suppression d'une ancienne image et miniature s'il elles existent
            if ($imageOld) {
                $imgOld = __DIR__.'/../../../web/'.$imageOld->getUploadDir().'/'.$urlOld;
                if ($urlOld !== $url && is_file($imgOld)) {
                    unlink($imgOld);
                    $imgMinOld = __DIR__.'/../../../web/mini/mini-'.$urlOld;
                    unlink($imgMinOld);
                }

                if (!isset($post['mini']) && $articleHasMini) {
                    unlink($imgMin);
                }
            }

            if (isset($post['mini']) && $post['mini']=='1') {

                $fileComplete = __DIR__.'/../../../web/image/'.$url;
                /** @var  $miniImageService */
                $miniImageService = $this->container->get('AppBundle.mini_image');
                $miniImageService->createMini($fileComplete);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $session = $this->get('Session');
            $session->getFlashBag()->add('info','L\'article a bien été modifié');
            return $this->redirectToRoute('article_edit', array('id' => $article->getId()));
        }

        return $this->render('article/edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Article $article)
    {
        $form = $this->createDeleteForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $image = $article->getImage();
            $url = $image->getUrl();
            $img = __DIR__.'/../../../web/'.$image->getUploadDir().'/'.$url;
            $imgMin = __DIR__.'/../../../web/mini/mini-'.$url;
            if (is_file($img)) {
                unlink($img);
            }
            if ($article->isMini() && is_file($imgMin)) {
                unlink($imgMin);
            }

            $em->remove($image);
            $em->remove($article);
            $em->flush();
            $session = $this->get('Session');
            $session->getFlashBag()->add('info','L\'article a bien été suprimé');
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * Creates a form to delete a Article entity.
     *
     * @param Article $article The Article entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $article->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
