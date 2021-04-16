<?php

namespace App\Controller;

use App\Repository\FicheRepository;
use App\Repository\MessageRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Form\ChangeParametersType;
use App\Form\ChangePasswordType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class MonCompteController extends AbstractController
{
    public function division($tmp) {
        $i=0;
        $k=1;

        if($tmp != null) {
            for ($i=0 ; $i<=count($tmp)-1 ; $i++) {
                if($i == 0) {
                    $posteurs[$i] = $tmp[$i];
                }
                elseif($i == count($tmp)-1) {
                    if ($tmp[$i-1] != $tmp[$i]) {
                        $posteurs[$k] = $tmp[$i];
                        $k++;
                    }
                }
                else {
                    if ($tmp[$i] != $tmp[$i-1]) {
                        $posteurs[$k] = $tmp[$i];
                        $k++;
                    }
                }
            }
            return $posteurs;
        }
    }

    /**
     * @Route("/mon_compte", name="mon_compte")
     */
    public function index(
        Security $security
    )
    {
        $currentUser = $security->getUser();

        $messages = $currentUser->getMessagesDestinataire();

        $i=0;
        $posteurs = null;
        $tmp = null;

        foreach($messages as $message) {
            $tmp[$i] = array([
                'posteur' => $message->getIdPosteur(),
                'topic' => $message->getIdTopic(),
            ]);
            $i++;
        }

        $posteurs = $this->division($tmp);

        return $this->render('mon_compte/index.html.twig', [
            'currentUser' => $currentUser,
            'posteurs' => $posteurs,
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/mon_compte/changer_parametre", name="changer_parametre")
     */
    public function changerParametre(
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    )
    {
        $user = $security->getUser();

        $form = $this->createForm(ChangeParametersType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $security->getUser();

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('mon_compte');
        }

        return $this->render('mon_compte/mes_contributions.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon_compte/voir_mes_fiches", name="voir_mes_fiches")
     */
    public function voirMesFiches(
        FicheRepository $frepo,
        Security $security
    )
    {
        $user = $security->getUser();

        $fiches = $frepo->findByAuthor($user);

        return $this->render('mon_compte/voir_mes_fiches.html.twig', [
            'fiches' => $fiches
        ]);
    }

    /**
     * @Route("/mon_compte/password", name="changer_mdp")
     */
    public function changeMdp(
        Security $security,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder)
    {
        $user = $security->getUser();
        if( $user === null ){
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mdp = $request->request->get('change_password')['oldMdp'];

            if($encoder->isPasswordValid($user, $mdp)){
                $mdpTest = $request->request->get('change_password')['newMdp'];
                $confirmMdp = $request->request->get('change_password')['confirmNewMdp'];

                if($mdpTest == $confirmMdp){
                    $newMdp = $encoder->encodePassword($user,$request->request->get('change_password')['newMdp']);
                    $user->setPassword($newMdp);

                    $manager->persist($user);
                    $manager->flush();
                    return $this->redirectToRoute('mon_compte');
                }
            }
        }

        return $this->render('mon_compte/change_password.html.twig', [
            'formCP'=> $form->createView()
        ]);
    }
}
