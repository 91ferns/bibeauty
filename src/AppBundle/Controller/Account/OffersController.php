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
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Controller\AdminAwareController;

class OffersController extends Controller implements AdminAwareController
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
     * @Route("/account/offers/{id}/{slug}/requeue/{offerId}", name="admin_business_offer_queue_path")
     * @Method("GET")
     */
    public function doQueueAction($id, $slug, $offerId, Request $request)
    {

        $repo = $this->getRepo('OfferAvailabilitySet');
        $avSet = $repo->findOneByOffer($offerId);

        if ($avSet) {

            $success = $this->doAvailabilities($avSet->getId());

        }

        // replace this example code with whatever you need
        return $this->redirectToRoute('admin_business_offers_path',["slug"=>$slug,"id"=>$id]);

    }

    /**
     * @Route("/account/offers/{id}/{slug}/new", name="admin_business_offers_new_path")
     * @Method({"GET"})
     */
    public function createAction($id, $slug, Request $request) {

        $business = $this->businessBySlugAndId($slug, $id);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Treatment");

        $treatments = $repository->findByBusiness($business);

        return $this->render(
            'account/offers/new.html.twig',
            array(
                'treatments' => $treatments,
                'business' => $business,
            )
        );
    }

    /**
     * @Route("/account/offers/{id}/{slug}/new", name="admin_offer_new_path")
     * @Method("POST")
     */
    public function createCheckAction($id, $slug, Request $request) {

        // Treatment ID comes from request then we just return the checkCreate

        $business = $this->businessBySlugAndId($slug, $id);

        $post = $request->get('offer');
        $em = $this->getDoctrine()->getManager();

        $failed = array();
        $total = count($post);
        $succeeded = array();

        $errors = array();

        if (count($post) > 0) {

            foreach($post as $offerData) {
                $data = (object) $offerData;

                $offer = $this->createOffer($business, $data, $error);

                if ($error) {
                    $errors[] = $error;
                    $failed[] = $offer;
                } else {
                    $succeeded[] = $offer;
                }

            }

            $numFailed = count($failed);

            if ($numFailed === $total) {
                // All failed
                $this->addFlash(
                    'error',
                    implode(' ', $errors)
                );
            } elseif ($numFailed > 0) {
                // Some failed
                $this->addFlash(
                    'error',
                    'We could not add ' . $failed . ' of your new offers.'
                );
                $em->flush();
            } else {
                $this->addFlash(
                    'notice',
                    'Successfully added all of your new offers.'
                );
                // All succeeded
                return $this->redirectToRoute('admin_business_offers_path',["slug"=>$slug,"id"=>$id]);
            }

        } else {
            $this->addFlash(
                'error',
                'Please enter some offers.'
            );
        }

        $repository = $em->getRepository("AppBundle:Treatment");
        $treatments = $repository->findByBusiness($business);

        return $this->render(
            'account/offers/new.html.twig',
            array(
                'treatments' => $treatments,
                'business' => $business,
            )
        );

    }

    public function createOffer($business, $data, &$error) {
        // this is absolutely something that would be offloaded to the worker

        //treatmentCategory
        //startDate
        //times
        //discountPrice
        //RecurrenceType
        // RecurrenceDates

        $slug = $business->getSlug();
        $id = $business->getId();

        if (!property_exists($data, 'treatmentCategory')) {
            $treatmentId = null;
        } else {
            $treatmentId = $data->treatmentCategory;
        }

        if (!property_exists($data, 'startDate')) {
            $date = false;
        } else {
            $date = $data->startDate;
        }

        if (!property_exists($data, 'times')) {
            $times = array();
        } else {
            $times = $data->times;
            if (!is_array($times)) {
                $times = array($times);
            }
        }

        if (!property_exists($data, 'recurrenceType')) {
            $recurrenceType = 'never';
        } else {
            $recurrenceType = $data->recurrenceType;
        }

        if($times[0] == 'ALL'){
          $times = $this->buildAllTimes();
        }
        if (!$date || !$times || count($times) < 1 ){
            $error = 'Date and time must be specified.';
        }

        if (!property_exists($data, 'discountPrice')) {
            $discount = false;
        } else {
            $discount = $data->discountPrice;
        }

        if (!$discount) {
            $error = 'You must specify a discount.';
        }

        if (!property_exists($data, 'recurrenceDates')) {
            $recurrenceDOWs = array();
        } else {
            $recurrenceDOWs = $data->recurrenceDOWs ? $data->recurrenceDOWs : array();
        }

        $recurrenceDOWs = array_unique($recurrenceDOWs);

        $treatments = $this->getRepo('Treatment');
        $treatment = $treatments->findOneBy(array( 'id' => $treatmentId ));

        if (!$treatment) {
            $error = 'Treatment not found.';
        }

        if ((float) $discount >= (float) $treatment->getOriginalPrice()) {
            $error = 'Discount must be cheaper than the original price.';
        }

        $validator = $this->get('validator');

        $em = $this->getDoctrine()->getManager();

        $offer = new Offer();
        $offer->setBusiness($business);
        $offer->setTreatment($treatment);
        $offer->setCurrentPrice($discount);

        $offerErrors = $validator->validate($offer);

        if (count($offerErrors) < 1) {
            $em->persist($offer);
        } else {
            $error = (string) $offerErrors;
            return $offer;
        }

        $startDateTime = new \DateTime($date);

        $availabilitySet = new OfferAvailabilitySet();
        $availabilitySet->setOffer($offer);
        $availabilitySet->setStartDate($startDateTime);
        $availabilitySet->setDaysOfTheWeek($recurrenceDOWs);
        $availabilitySet->setTimes($times);
        $availabilitySet->setTreatment($treatment);
        $availabilitySet->setRecurrenceType($recurrenceType);

        $availabilityErrors = $validator->validate($availabilitySet);

        if (count($availabilityErrors) < 1) {
            $em->persist($availabilitySet);
        } else {
            $em->remove($offer);
            $error = (string) $availabilityErrors;
            return $offer;
        }

        $em->flush();
        $success = $this->doAvailabilities($availabilitySet->getId());

        // we now need to create the availability set

        if (!$success) {
            $em->remove($availabilitySet);
            $em->remove($offer);
            $em->flush();
            return 'Unable to queue your availabilities. Please report this to us on the contact us page.';
        }

        return $offer;
    }


    protected function buildAllTimes() {
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
     * @Route("/ajax/offers/recurrenceform", name="ajax_delete_offer")
     * @Method("GET")
     */
    public function offerForm(Request $request) {
        return $this->render(
            'account/offers/form.html.twig',
            array(
                'prefix' => $request->query->get('prefix'),
                'index' => $request->query->get('index'),
            )
        );
    }

    /**
     * @Route("/account/offers/{id}/{slug}/remove", name="admin_delete_offer")
     * @Method("POST")
     */
    public function deleteOffer($id, $slug, Request $request)
    {
      $req    = $request->request;
      $offerIds = $req->get('offers', false);
      $em = $this->getDoctrine()->getManager();

      $offers = $em->getRepository("AppBundle:Offer")->findById($offerIds);

      if (count($offers) > 0) {

          foreach ($offers as $offer){
            $em->remove($offer);
          }
          $em->flush();

      }

      return new Response('Removed ' . count($offers) . ' offers.');
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

        try {
            $rabbitmq = $this->get('old_sound_rabbit_mq.create_availabilities_producer');
            $rabbitmq->publish($availabilitySetId);
            return true;
        } catch (\Exception $e) {
            return false;
        }

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
