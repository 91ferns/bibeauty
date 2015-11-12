<?php

namespace AppBundle\Controller\Account;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\ApplicationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Form\BookingType;
use AppBundle\Form\ServiceType;

use AppBundle\Entity\Business;
use AppBundle\Entity\Service;

use AppBundle\Entity\OfferAvailabilitySet;
use AppBundle\Entity\Offer;

use Symfony\Component\Process\Process;

class OffersController extends Controller
{
    /**
     * @Route("/account/offers/{id}/{slug}", name="admin_business_offers_path")
     * @Method("GET")
     */
    public function indexAction($id, $slug, Request $request)
    {
        $offers   = $this->getRepo('Offer');
        $business = $this->businessBySlugAndId($slug, $id);

        $offers   = $offers->findByBusiness($business);

        //print_r($offers);

        return $this->render('account/offers/index.html.twig', array(
            'offers' => $offers,
            'business' => $business,
        ));
    }

    /**
     * @Route("/account/offers/show/{id}/{slug}/{bookingId}/", name="admin_show_booking_path")
     * @Method("GET")
     */
    public function showAction($id, $slug, $bookingId, Request $request)
    {
        $booking = new Booking();
        $booking->find($bookingId);

        // replace this example code with whatever you need
        return $this->render('account/offers/show.html.twig', array(
            'booking' => $booking,
            'business' => $booking->getBusiness(),
            'service' => $booking->getService(),
            'serviceType'=> $service->getServiceType(),
        ));
    }


    /**
     * @Route("/account/offers/{id}/{slug}/new", name="admin_business_offers_new_path")
     * @Method({"GET"})
     */
    public function createAction($id, $slug, Request $request) {
       //$booking = new Booking();

       //$form = $this->createForm(new BookingType(), $booking);
       /*$business = $this->businessBySlugAndId($slug, $id);
       $em         = $this->getDoctrine()->getManager();
       $treatments = $em->getRepository("AppBundle:Business")->findBusinessTreatmentsCategory($business);*/
       $offers   = $this->getRepo('Offer');
       $business = $this->businessBySlugAndId($slug, $id);
       $offers   = $offers->findOffersByBusinessByCategory($business);

      /*foreach($offers as $cat => $offs){
        echo $cat .':<br>';
        foreach($offs as $off){
          echo $off->getTreatment()->getName() .'<br>';
        }
      }*/

      $treatments = $this->getRepo('Business')->findBusinessTreatmentsCategory($business);
      return $this->render(
         'account/offers/new.html.twig',
         array(
            'business'   => $business,
            'treatments' => $treatments,
            'offers'     => $offers,
         )
      );
    }

    /**
     * @Route("/account/offers/{id}/{slug}/new", name="admin_offer_new_path")
     * @Method("{GET,POST}")
     */
    public function createCheckAction($id, $slug, Request $request) {
         $offers     = $this->getRepo('Offer');
         $business   = $this->businessBySlugAndId($slug, $id);
         $offers     = $offers->findOffersByBusinessByCategory($business);
         $treatments = $em->getRepository("AppBundle:Business")->findBusinessTreatmentsCategory($business);
         var_dump($business.getId());exit;
         return $this->render(
            'account/offers/new.html.twig',
            array(
               'business'   => $business,
               'treatments' => $treatments,
               'offers'     => $offers,
            )
         );

        // Treatment ID comes from request then we just return the checkCreate
        /*$treatmentId = $request->request->get('Treatment', false);

        if (!$treatmentId) {
            return $this->redirectToRoute('admin_business_offers_path', array(
                'id' => $id,
                'slug' => $slug
            ));
        }*/
        return $this->render(
           'account/offers/new.html.twig',
           array(
              'business'   => $business,
              'treatments' => $treatments,
              'offers'     => $offers,
           )
        );

        return $this->checkCreateAvailabilityAction($id, $slug, $treatmentId, $request);

    }

    /**
     * @Route("/account/offers/{id}/{slug}/{treatmentId}/new", name="admin_treatment_availability_new_path")
     * @Method("POST")
     */
    public function checkCreateAvailabilityAction($id, $slug, $treatmentId, Request $request) {
        // this is absolutely something that would be offloaded to the worker
        $business = $this->businessBySlugAndId($slug, $id);

        $date = $request->request->get('Date', false);
        $times = $request->request->get('Times', array());
        $recurrenceType = $request->request->get('RecurrenceType', 'never');
        if($times[0] == 'ALL'){
          $times = $this->buildAllTimes();
        }
        if (!$date || !$times || count($times) < 1 ){
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Date and time must be specified.'
            ));
        }

        $discount = $request->request->get('Discount', false);

        if (!$discount) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'You must specify a discount.'
            ));
        }

        $recurrenceDOWs = $request->request->get('RecurrenceDates', array());
        $recurrenceDOWs = array_unique($recurrenceDOWs);

        $treatments = $this->getRepo('Treatment');
        $treatment = $treatments->findOneBy(array( 'id'=>$treatmentId ));

        if (!$treatment) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Treatment not found.'
            ));
        }

        if ((float) $discount >= (float) $treatment->getOriginalPrice()) {
            return $this->redirectToRoot($slug, $id, $treatmentId, array(
                'error',
                'Discount must be cheaper than the original price.'
            ));
        }


        $em = $this->getDoctrine()->getManager();

        $offer = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $offer->setCurrentPrice($discount);
        $em->persist($offer);

        $startDateTime = new \DateTime($date);

        $availabilitySet = new OfferAvailabilitySet();
        $availabilitySet->setOffer($offer);
        $availabilitySet->setStartDate($startDateTime);
        $availabilitySet->setDaysOfTheWeek($recurrenceDOWs);
        $availabilitySet->setTimes($times);
        $availabilitySet->setTreatment($treatment);
        $availabilitySet->setRecurrenceType($recurrenceType);

        $em->persist($availabilitySet);
        $em->flush();

        /*
        $root = $this->get('kernel')->getRootDir();
        $env = $this->get('kernel')->getEnvironment();

        $processText = sprintf('php %s/console bibeauty:generate:availabilities %d --env=%s',
            $root,
            $availabilitySet->getId(),
            $env
        );

        $process = new Process($processText);
        $process->run(); */

        $success = $this->doAvailabilities($availabilitySet->getId());

        // we now need to create the availability set

        if ($success) {
            $message = 'Queued the creation of your availabilities.';
        } else {
            $em->remove($availabilitySet);
            $em->remove($offer);
            $em->flush();
            $message = 'Unable to queue your availabilities. Please report this to us on the contact us page.';
        }

        return $this->redirectToRoot($slug, $id, $treatmentId, array(
            'notice',
            $message
        ));
    }
    protected function buildAllTimes()
    {
      $times = [];
        for($i=7;$i<=21;$i++){
          for($j=0;$j<=3;$j++){
            $min = $j*15;
            if($min == 0) $min = '00';
            $times[] = $i . ':' . $min;
          }
        }
        return $times;
    }
    /**
     * @Route("/account/offers/{id}/{slug}/addedit", name="admin_business_offers_addedit_path")
     * @Method("POST")
     */
  public function addEditAction($id, $slug, Request $request) {
      $req = $request->request;
      $em   = $this->getDoctrine()->getManager();
      $business = $this->businessBySlugAndId($slug, $id);
      $times = $req->get('Times', array());
      $recurrenceTypes = $req->get('RecurrenceType', 'never');
      $prices = $req->get('CurrentPrice', array());
      $businessId = $id;
      $ids    = $req->get('id', array());
      /*echo '<pre>';
      var_dump($ids);echo '<br>';
      var_dump($times); echo '<br>';
      var_dump($recurrenceTypes);echo '<br>';
      var_dump($prices);*/

      if(!empty($ids)){
        $offers = $em->getRepository("AppBundle:Offer");

        /** UPDATE EXISTING **/
        foreach($ids as $k=>$id){
          $this->returnWithError($times[$k], 'Time ');
          $this->returnWithError($prices[$k], 'Current Price ');
          $offers->doUpdate($id,$times[$k],$prices[$k],$recurrenceTypes[$k]);
        }
      }


      /** INSERT NEW OFFERS **/
      $treatments      = $req->get('newTreatment');
      if(!empty($treatments)){
        $dates           = $req->get('newStartDate');
        $times           = $req->get('newTimes');
        $prices          = $req->get('newCurrentPrice');
        $recurrenceTypes = $req->get('newRecurrenceType', 'never');
        foreach($treatments as $k=>$tx){
          $this->doCreate($slug, $id, $business, $tx, $dates[$k], $times[$k], $prices[$k],$recurrenceTypes[$k][0]);
        }
        $em->flush();
      }
      $this->get('session')->getFlashBag()->add('notice','Treatments successfully created/updated.');
      return  $this->redirectToRoute('admin_business_offers_new_path',  ["slug"=>$slug,"id"=>$businessId]);
    }

    function returnWithError($val,$field)
    {
      if (empty($val) || !$val || count($val) < 1) {
        $this->get('session')->getFlashBag()->add('error', $field . ' cannot be empty. Please check and resubmit.');
          return $this->redirectToRoute('admin_business_treatments_new_path',["slug"=>$slug,"id"=>$id]);
      }
      return false;
    }

    protected function doCreate($slug, $id, $business, $treatmentId, $date, $times, $discount,$recurrenceType,$recurrenceDOWs = array())
    {
      if (!$date || !$times || count($times) < 1 ){
          return $this->redirectToRoot($slug, $id, $treatmentId, array(
              'error',
              'Date and time must be specified.'
          ));
      }

      if (!$discount) {
          return $this->redirectToRoot($slug, $id, $treatmentId, array(
              'error',
              'You must specify a discount.'
          ));
      }
    $recurrenceDOWs = array_unique($recurrenceDOWs);

    $treatments = $this->getRepo('Treatment');
    $treatment = $treatments->findOneBy(array( 'id'=>$treatmentId ));
  //  var_dump($treatment);
    if (!$treatment) {
        return $this->redirectToRoot($slug, $id, $treatmentId, array(
            'error',
            'Treatment not found.'
        ));
    }

    if ((float) $discount >= (float) $treatment->getOriginalPrice()) {
        return $this->redirectToRoot($slug, $id, $treatmentId, array(
            'error',
            'Discount must be cheaper than the original price.'
        ));
    }


    $em = $this->getDoctrine()->getManager();

    $offer = new Offer();
    $offer->setBusiness($business);
    $offer->setTreatment($treatment);
    $offer->setCurrentPrice($discount);
    $em->persist($offer);

    $startDateTime = new \DateTime($date);

    $availabilitySet = new OfferAvailabilitySet();
    $availabilitySet->setOffer($offer);
    $availabilitySet->setStartDate($startDateTime);
    $availabilitySet->setDaysOfTheWeek($recurrenceDOWs);
    $availabilitySet->setTimes($times);
    $availabilitySet->setTreatment($treatment);
    $availabilitySet->setRecurrenceType($recurrenceType);

    $em->persist($availabilitySet);
    $em->flush();

    /*
    $root = $this->get('kernel')->getRootDir();
    $env = $this->get('kernel')->getEnvironment();

    $processText = sprintf('php %s/console bibeauty:generate:availabilities %d --env=%s',
        $root,
        $availabilitySet->getId(),
        $env
    );

    $process = new Process($processText);
    $process->run(); */

    $success = $this->doAvailabilities($availabilitySet->getId());
  //  var_dump($success);
  //  var_dump($availabilitySet->getId());exit;
    // we now need to create the availability set

    if ($success) {
        $message = 'Queued the creation of your availabilities.';
    } else {
        $em->remove($availabilitySet);
        $em->remove($offer);
        $em->flush();
        $message = 'Unable to queue your availabilities. Please report this to us on the contact us page.';
    }
    return $message;
  }
    /**
     * @Route("/account/offers/{id}/{slug}/toggleEnabled", name="admin_treatment_toggle_is_open")
     * @Method("POST")
     */
    public function updateOfferStatusAction($id,$slug,Request $request)
    {
      $req       = $request->request;
      $offerid   = $req->get('offerId',false);
      $isOpen    = $req->get('onoffswitch',false);
      $isOpen    = ($isOpen == 'on') ? true : $isOpen;
      $em = $this->getDoctrine()->getManager();
      $offer    = $em->getRepository("AppBundle:Offer")->findOneBy(['id'=>$offerid]);
      $offer->setIsOpen($isOpen);
      $em->flush();
      return $this->redirectToRoute('admin_business_offers_path',['id'=>$id,'slug'=>$slug]);
    }
    /**
     * @Route("/account/offers/{id}/{slug}/remove", name="admin_delete_offer")
     * @Method("POST")
     */
    public function deleteOffer($id,$slug,Request $request)
    {
      $req    = $request->request;
      $offers = $req->get('offers',false);
      foreach ($offers as $offerid){
        $em = $this->getDoctrine()->getManager();
        $offer    = $em->getRepository("AppBundle:Offer")->findOneBy(['id'=>$offerid]);
        $em->persist($offer);
        $em->remove($offer);
      }
      $em->flush();
      //return true;//$this->redirectToRoute('admin_business_offers_path',['id'=>$id,'slug'=>$slug]);
      return new Response();
 }
    protected function redirectToRoot($slug, $id, $treatmentId, $flash = false) {
        if ($flash) {
            list($type, $message) = $flash;
            $this->addFlash(
                $type,
                $message
            );
        }
        return $this->redirectToRoute(
            'admin_business_offers_path',
            array( "slug"=> $slug,
                   "id"=> $id,
                   "treatmentId"=> $treatmentId
            )
        );
    }

    protected function doAvailabilities($availabilitySetId) {

        /*try {
            $this->get('old_sound_rabbit_mq.create_availabilities_producer')->publish($availabilitySetId);
            return true;
        } catch (\Exception $e) {
            return false;
        }*/


        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $offerAvailability = $em->getRepository("AppBundle:OfferAvailabilitySet");

        $availabilitySet = $offerAvailability->findOneById($availabilitySetId);

        if (!$availabilitySet) {
            return false;
        }

        // Start it going
        $business = $availabilitySet->getTreatment()->getBusiness();

        // Delete previous availabilities to make this rerunable
        $queryBuilder = $em
            ->createQueryBuilder()
            ->delete('AppBundle:Availability', 'a')
            ->innerJoin('a.availabilitySet', 's')
            ->where('a.availabilitySet = :availabilitySet')
            ->setParameter(':availabilitySet', $availabilitySet);

        $queryBuilder->getQuery()->execute();

        // Offer is made. We need to make its availability now
        $matchingDates = $availabilitySet->datesThatMatchRecurrence();
        // $availabilitySets = $availabilitySet->datesToAvailabilities($matchingDates, $business);
        // $treatment = $availabilitySet->getTreatment();

        $batchSize = 20;

        foreach($matchingDates as $i => $date) {
            $x = new \AppBundle\Entity\Availability();
            $x->setDate($date);
            $x->setAvailabilitySet($availabilitySet);
            $x->setTreatment($availabilitySet->getTreatment());
            $x->setBusiness($business);
            $em->persist($x);

            if ($i !== 0 && ($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches objects

                $availabilitySet = $offerAvailability->findOneById($availabilitySetId);
                $business = $availabilitySet->getTreatment()->getBusiness();

            }
        }

        $availabilitySet->setProcessed(true);

        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();
        // End it

        return count($i);

    }

    /**
     * @Route("/account/treatments/edittimes", name="admin_business_offers_edittime_path")
     * @Method({"POST"})
     */
    public function editTimesAction(Request $request)
    {
      $req    = $request->request;
      $id     = $req->get('pk',false);
      $times  = $req->get('Times', array());
      $em = $this->getDoctrine()->getManager();
      $offers = $em->getRepository("AppBundle:Offer");
      $offer  = $offers->findOneBy(['id'=>$id]);
      $offer->setTimes($times);
      return new JsonResponse(array('success' => true));
    }

    /**
     * @Route("/account/offers/{id}/{slug}/edit", name="admin_edit_offer")
     * @Method("POST")
     */
     function editOfferAction($id, $slug, Request $request)
     {
       $req    = $request->request;
       $id     = $req->get('pk',false);
       $price  = str_replace('$','',$req->get('value',false));
       $em = $this->getDoctrine()->getManager();
       $offers = $em->getRepository("AppBundle:Offer");
       $offer  = $offers->findOneBy(['id'=>$id]);
       $offer->setCurrentPrice($price);
       $em->flush();
       return new JsonResponse(array('success' => true));
     }

}
