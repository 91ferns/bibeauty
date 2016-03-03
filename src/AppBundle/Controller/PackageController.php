<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

class PackageController extends ApplicationController
{
    /**
     * @Route("/package", name="package_path")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        return $this->redirectToRoute('package_therapist_path');

        // return $this->render('package/index.html.twig', array());

    }

    /**
     * @Route("/package/therapist", name="package_therapist_path")
     * @Method("GET")
     */
    public function therapistAction(Request $request)
    {
        return $this->render('package/therapist.html.twig', array(
            'therapists' => $this->getTherapists()
        ));

    }

    /**
     * @Route("/package/therapist/{id}", name="package_summary_path")
     * @Method("GET")
     */
    public function summaryAction(Request $request, $id)
    {
        return $this->render('package/summary.html.twig', array(
            'id' => $id
        ));

    }

    /**
     * @Route("/package/therapist/{id}/summary", name="package_therapist_summary_path")
     * @Method("GET")
     */
    public function therapistSummaryAction(Request $request, $id)
    {

        $therapist = $this->getTherapists($id);

        if (!$therapist) {
            throw $this->createNotFoundException('This therapist does not exist');
        }

        return $this->render('package/therapist-summary.html.twig', array(
            'therapist' => $therapist
        ));

    }

    /**
     * @Route("/voucher/{id}", name="package_voucher_path")
     * @Method("GET")
     */
    public function voucherAction(Request $request, $id)
    {
        $therapist = $this->getTherapists($id);

        if (!$therapist) {
            throw $this->createNotFoundException('This therapist does not exist');
        }

        return $this->render('package/voucher.html.twig', array(
            'therapist' => $therapist,
            'expiry' => new \DateTime('+3 months')
        ));

    }


    private function getTherapists($id = null) {

        $devin = (object) array(
            'therapist' => 'Devin',
            'business' => 'MetroMD',
            'location' => 'Hollywood',
            'addressl1' => '7080 Hollywood Blvd',
            'addressl2' => 'Los Angeles, CA 90028',
            'name' => 'Stem Cell Facial & Microdermabrasion',
            'description' => 'MetroMD is world premier anti-aging clinic. The stem cell facial helps rejuvenate  skin and promote a more youthful look. During this facial the client will receive a microdermabrasion to remove dead skin and damaged surface skin. From here a series of serums including a stem cell serum is applied to hydrate the skin and promote rejuvenation. After the treatment the patient will be glowing and ready for any event.',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'http://www.yelp.com/biz/metromd-los-angeles-4',
            'paypal' => 'XG54EPUUANM8Y',
            'image' => 'MetroMD.jpg',
            'phone' => '(323) 285-5300'
        );

        $abanara = (object) array(
            'therapist' => 'Aban Patel',
            'business' => 'Abanara Skin Esthetics',
            'location' => 'Inglewood',
            'addressl1' => '8923 Reading Ave.',
            'addressl2' => 'Los Angeles, CA 90045',
            'name' => 'Aromatherapy Galvanic Infusion Facial',
            'description' => 'Deep pore cleansing, Galvanic infusion therapy according to skin type, pressure point and lymphatic facial and delicate massage with a special mask with a deep penetration treatment. Lastly, moisturizer is applied with a blast of pure oxygen.',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'http://www.yelp.com/biz/abanara-skin-esthetics-los-angeles',
            'paypal' => '4CCYY3D6J795Q',
            'image' => 'Abanara.jpg',
            'phone' => '(310) 779-9098'
        );

        $vibrations = (object) array(
            'therapist' => 'Ingrid Thompson',
            'business' => 'High Vibration Skincare',
            'location' => 'Universal City',
            'addressl1' => '3401 Barham Blvd.',
            'addressl2' => 'Universal City, CA 90068',
            'name' => 'Beauty Flash',
            'description' => 'Your 45 minute treatment will begin with a gentle cleanse followed by RED & BLUE LED Light Therapy to improve cellular function, tone and brightness.  Next, galvanic currents and sound waves deliver a powerful cocktail of lactic acid, vitamins A, C & E and peptides deep into the skin to help refine skin texture, enhance natural exfoliation, brighten skin tone, prevent & correct environmental damage, stimulate collagen and elastin. 3 powerful technologies and advanced ingredients are combined to deliver instant results while effectively and gently restore youthfulness to your skin and get you ready for that big event!',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'http://www.yelp.com/biz/high-vibration-skincare-los-angeles',
            'paypal' => '7K9S76SE4GQTE',
            'image' => 'Vibrations.jpg',
            'phone' => '(323) 842-447'
        );

        $amanda = (object) array(
            'therapist' => 'Amanda',
            'business' => 'Salon On Main',
            'location' => 'Downtown',
            'addressl1' => '12777 W. Jefferson Blvd. Bldg. D, Third Floor',
            'addressl2' => 'Los Angeles, CA 90066',
            'name' => 'Customized Cleansing Aromatherapy & Muscle Stimulation Facial',
            'description' => 'We start off with some aromatherapy steam to relax you, and to open and prep your pores for cleansing. Next we follow with 2 gentle cleansings to remove all dirt, oil, & makeup. Then we follow up by scrubbing to remove dead skin cells. Finally, we finish with toner to seal the cuticle, moisturize, and facial massage to stimulate the facial muscles and slow down the aging process. We use only the finest Ayur-Medic Products with ingredients found in nature with latest technological advances in skincare. The results leave you feeling soft, clean, and rejuvenated!',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'http://www.yelp.com/biz/salon-on-main-los-angeles',
            'paypal' => 'WTG6J6UN25U4Q',
            'image' => 'Amanda.jpg',
            'phone' => '(213) 626-2131'
        );

        $vanity = (object) array(
            'therapist' => 'Daniela Rivera',
            'business' => 'Me Myself & Vanity',
            'location' => 'Marina Del Rey',
            'addressl1' => '13455 Maxella Ave. Ste. 103',
            'addressl2' => 'Marina Del Rey, CA 90292',
            'name' => 'Custom Tailored Aromatherapy Facial',
            'description' => 'Relax and enjoy a tailored aromatherapy facial, that includes cleanse, extractions, masque, and a shoulder massage. Your skin will feel fresh and renewed asking for more! We proudly use Farmhouse Fresh and Young Living Essential oils in our facials. You will love our organic ingredients, and each service is customized for your skin’s needs.  Oprah calls our line a favorite, and a must have! Come see for yourself what awaits you in this cozy little studio.',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'http://www.yelp.com/biz/me-myself-and-vanity-marina-del-rey-2',
            'paypal' => 'B76XWQU7WYU5E',
            'image' => 'Vanity.jpg',
            'phone' => '(424) 371-9645'
        );

        $seti = (object) array(
            'therapist' => 'Seti Mayaet',
            'business' => 'Enliven Your Skin',
            'location' => 'Playa Vista',
            'addressl1' => '12777 W. Jefferson Blvd. Bldg. D, Third Floor',
            'addressl2' => 'Los Angeles, CA 90066',
            'name' => 'Enliven Just You Custom Facial',
            'description' => 'This customized treatment is designed to leave you looking and feeling recharged and refreshed. It\'s the perfect balancing remedy for those who haven\'t had a facial in a while, or need to look their best for a special occasion. Your facial includes: a power peel that won\'t leave you shedding, extractions, specialty masque, light therapy, serum cocktail, and finishing soufflé.',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'http://www.yelp.com/biz/enliven-your-skin-los-angeles',
            'paypal' => '2Q6HK5XQ38926',
            'image' => 'Seti.jpg',
            'phone' => '(310) 880-2820'
        );

        $adrienne = (object) array(
            'therapist' => 'Adrienne K Goncz',
            'business' => 'Adrienne Skin Care',
            'location' => 'Sherman Oaks',
            'addressl1' => '15450 Ventura Blvd, Ste 104',
            'addressl2' => 'Sherman Oaks, CA 91403',
            'name' => 'Rejuvenating Facial',
            'description' => 'Custom tailored facial for both women and men.  This unique treatment will relax your mind, and dramatically improve the texture and tone of your skin. The facial includes an enzyme exfoliation, custom formulated mask, a face and décolleté massage, serum, toner, and moisturizer which leaves your skin toned, glowing, and moisturized.',
            'duration' => '45 mins',
            'stars' => 4,
            'reviews' => 'http://www.yelp.com/biz/adrienne-skin-care-sherman-oaks',
            'paypal' => 'UJPWCPT2L6ELJ',
            'image' => 'Adrienne.jpg',
            'phone' => '(818) 357-9225'
        );

        $meridan = (object) array(
            'therapist' => 'Abraham Sauma',
            'business' => 'Meridian Day Spa ',
            'location' => 'West Hollywood',
            'addressl1' => '808 Hilldale Ave',
            'addressl2' => 'West Hollywood, CA 90069',
            'name' => 'Meridian European Facial',
            'description' => 'This facial is individually tailored to meet your skin care needs. Our customized facial begins with a thorough analysis to address skin goals and concerns. The facial includes a deep cleanse, tone, exfoliation, steam, extractions, massage, mask and finishing cream which will leave your skin healthy and refreshed.',
            'duration' => '45 mins',
            'stars' => 5,
            'reviews' => 'https://www.yelp.com/biz/meridian-day-spa-west-hollywood-5',
            'paypal' => 'SD6SHQ5A27BXY',
            'image' => 'Meridan.jpg',
            'phone' => '(310) 601-7633'
        );

        $arr = array(
            $devin,
            $abanara,
            $vibrations,
            $amanda,
            $vanity,
            $seti,
            $adrienne,
            $meridan
        );

        if ($id !== null) {

            if (array_key_exists($id, $arr)) {
                return $arr[$id];
            }

            return false;
        }

        return $arr;
    }




}
