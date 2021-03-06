<?php

namespace Club\MatchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/match/match")
 */
class MatchController extends Controller
{
    /**
     * @Route("/recent/{limit}")
     */
    public function recentAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $matches = $em->getRepository('ClubMatchBundle:Match')->getRecentMatches(null, $limit);

        return $this->render('ClubMatchBundle:Match:recent_matches.html.twig', array(
            'matches' => $matches
        ));
    }

    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $res = array();
        $res['user0'] = $this->getUser();

        $sets = 5;
        $form = $this->get('club_match.match')->getMatchForm($res, $sets);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());
            if ($form->isValid()) {

                $this->get('club_match.match')->bindMatch($form->getData());

                if ($this->get('club_match.match')->isValid()) {
                    $this->get('club_match.match')->save();
                    $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('Your changes are saved.'));

                    return $this->redirect($this->generateUrl('club_match_match_index'));
                } else {
                    $this->get('session')->getFlashBag()->add('error', $this->get('club_match.match')->getError());
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'sets' => $sets
        );
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $match = $em->find('ClubMatchBundle:Match',$id);

        $em->remove($match);

        $event = new \Club\MatchBundle\Event\FilterMatchEvent($match);
        $this->get('event_dispatcher')->dispatch(\Club\MatchBundle\Event\Events::onMatchDelete, $event);

        $em->flush();
        $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('Your changes are saved.'));

        return $this->redirect($this->generateUrl('club_match_match_index'));
    }

    /**
     * @Route("/show/{id}")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $match = $em->find('ClubMatchBundle:Match',$id);

        return array(
            'match' => $match
        );
    }

    /**
     * @Route("", defaults={"page" = 1})
     * @Route("/{page}", name="club_match_offset")
     */
    public function indexAction($page)
    {
        $results = 35;
        $em = $this->getDoctrine()->getManager();
        $paginator = $em->getRepository('ClubMatchBundle:Match')->getPaginator($results, $page);

        $this->get('club_extra.paginator')
            ->init($results, count($paginator), $page, 'club_match_offset');

        return $this->render('ClubMatchBundle:Match:index.html.twig', array(
            'matches' => $paginator
        ));
    }
}
