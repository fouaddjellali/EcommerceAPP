<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- USERS ---
        $roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_BANNED'];
        $users = [];
        foreach ($roles as $role) {
            $user = new User();
            $user->setEmail(strtolower($role) . '@example.com');
            $user->setRoles([$role]);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[$role] = $user;
        }
        // --- CATEGORIES ---
        $categories = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setName("Category $i");
            $manager->persist($category);
            $categories[] = $category;
        }
        // --- PRODUCTS ---
        $products = [];
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName("Product $i")
                ->setDescription("Description for Product $i")
                ->setPrice(mt_rand(10, 100))
                ->setCategory($categories[array_rand($categories)]);
            $manager->persist($product);
            $products[] = $product;
        }
        // --- ORDERS ---
        for ($i = 1; $i <= 5; $i++) {
            $order = new Order();
            $order->setOrderDate(new \DateTimeImmutable());
            // Ajouter des produits aléatoires à la commande
            $selectedProducts = array_rand($products, mt_rand(2, 5));
            foreach ((array) $selectedProducts as $productIndex) {
                $order->addProduct($products[$productIndex]);
            }
            $manager->persist($order);
        }
        // --- COMMENTS ---
        for ($i = 1; $i <= 20; $i++) {
            $comment = new Comment();
            $comment->setContent("This is a comment for product $i.")
                ->setCreatedAt(new \DateTimeImmutable())
                ->setProduct($products[array_rand($products)]);
            $manager->persist($comment);
        }
        $manager->flush();
    }
}