<?php


namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;

/**
 * @OASecurity(name="Bearer")
 */
class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

}