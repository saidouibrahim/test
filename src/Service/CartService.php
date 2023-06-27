<?php
namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function addToCart(int $id){

        $cart = $this->requestStack->getSession()->get('cart', []);
         
        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }

        $this->getSession()->set('cart', $cart);
    }


    public function removeToCart(int $id){
        $cart = $this->requestStack->getSession()->get('cart', []);

        unset($cart[$id]);
        return $this->getSession()->set('cart', $cart);
    }




    public function revoveCarteAll(){
        return $this->getSession()->remove('cart');

    }


    public function getTotal(): array
    {
        $cart = $this->getSession()->get('cart');
        $cartdata = [];
        if($cart){
            foreach($cart as $id => $quantity){
                $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
                if(!$product){
                    //supprimer le produit puis continuer en sortant de la boucle
                }
                $cartdata[] = [
                'product' => $product,
                'quantity' => $quantity,
                ];
            } 
        }
        return $cartdata;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

}