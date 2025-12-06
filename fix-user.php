<?php

require __DIR__.'/vendor/autoload.php';

use App\Entity\User;

$kernel = new App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

$entityManager = $container->get('doctrine')->getManager();
$passwordHasher = $container->get('security.user_password_hasher');

// Supprimer l'ancien utilisateur
$oldUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@pharma.local']);
if ($oldUser) {
    $entityManager->remove($oldUser);
    $entityManager->flush();
    echo "Ancien utilisateur supprimé\n";
}

// Créer le nouvel utilisateur
$user = new User();
$user->setEmail('admin@pharma.local');
$user->setRoles(['ROLE_USER']);
$user->setIsActive(true);

if (method_exists($user, 'setFirstName')) {
    $user->setFirstName('Admin');
}
if (method_exists($user, 'setLastName')) {
    $user->setLastName('PharmaPro');
}

$hashedPassword = $passwordHasher->hashPassword($user, 'admin123');
$user->setPassword($hashedPassword);

$entityManager->persist($user);
$entityManager->flush();

echo "✅ UTILISATEUR CRÉÉ !\n";
echo "Email: admin@pharma.local\n";
echo "Password: admin123\n";
echo "\nAllez sur http://localhost:8000/login\n";