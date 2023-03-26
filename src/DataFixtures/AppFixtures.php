<?php

namespace App\DataFixtures;

use App\Entity\User;
use Base\Entity\Layout\Attribute\Hyperlink;
use Base\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Proxies\__CG__\Base\Entity\Layout\Attribute\Adapter\HyperpatternAdapter;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User("user@example.org", "User", "Test");
        $user->setIsVerified(true)->setIsApproved(true);
        $user->setRoles([UserRole::SUPERADMIN]);
        $user->setPlainPassword("user");
        $manager->persist($user);

        $hyperpattern = new HyperpatternAdapter();
        $hyperpattern->setLabel("Website URL");
        $hyperpattern->setPattern("https://{0}/{1}");
        $hyperpattern->setPlaceholder(["www.example.org", "my/path"]);
        $manager->persist($hyperpattern);

        $user->addHyperlink(new Hyperlink($hyperpattern, ["www.glitchr.io", "my.path"]));
        $manager->flush();
    }
}
