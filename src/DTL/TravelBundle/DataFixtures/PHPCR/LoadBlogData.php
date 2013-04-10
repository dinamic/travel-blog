<?php

namespace DTL\MainBundle\DataFixtures\PHPCR;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Cmf\Bundle\BlogBundle\Document\Blog;
use Symfony\Cmf\Bundle\BlogBundle\Document\Post;
use PHPCR\Util\NodeHelper;

class LoadBlogData implements FixtureInterface, OrderedFixtureInterface
{
    public function getOrder()
    {
        return 10;
    }

    public function load(ObjectManager $dm)
    {
        $session = $dm->getPhpcrSession();

        NodeHelper::createPath($session, '/cms/content');
        NodeHelper::createPath($session, '/cms/routes');

        $root = $dm->find(null, '/cms/content');

        $this->faker = \Faker\Factory::create();

        $blog = new Blog;
        $blog->setName('DTLs Blog');
        $blog->setParent($root);
        $dm->persist($blog);

        for ($i = 1; $i <= 20; $i++) {
            $p = new Post;
            $p->setTitle($this->faker->text(30));
            $p->setDate(new \DateTime($this->faker->date));
            $p->setBody($this->faker->text(500));
            $p->setBlog($blog);
            $dm->persist($p);
        }

        $dm->flush();
    }
}
