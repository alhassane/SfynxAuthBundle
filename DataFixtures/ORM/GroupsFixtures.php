<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Authentication
 * @package    DataFixtures
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2011-12-28
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Sfynx\AuthBundle\Domain\Entity\Group;

/**
 * Groups DataFixtures.
 *
 * @subpackage   Authentication
 * @package    DataFixtures
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class GroupsFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load group fixtures
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2011-12-28
     */
    public function load(ObjectManager $manager)
    {

        $field0 = new Group('Groupe Subscriber', array('ROLE_SUBSCRIBER'));
        $field0->setEnabled(true);
        $field0->setPermissions(array('VIEW'));
        $manager->persist($field0);

        $field0_bis = new Group('Groupe Member', array('ROLE_MEMBER'));
        $field0_bis->setEnabled(true);
        $field0_bis->setPermissions(array('VIEW'));
        $manager->persist($field0_bis);

        $field1 = new Group('Groupe User', array('ROLE_USER'));
        $field1->setEnabled(true);
        $field1->setPermissions(array('VIEW'));
        $manager->persist($field1);

        $field2 = new Group('Groupe Admin', array('ROLE_ADMIN'));
        $field2->setEnabled(true);
        $field2->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $manager->persist($field2);

        $field3 = new Group('Groupe Super Admin', array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'));
        $field3->setEnabled(true);
        $field3->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $manager->persist($field3);

        $field4 = new Group('Groupe Manager', array('ROLE_CONTENT_MANAGER'));
        $field4->setEnabled(true);
        $field4->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $manager->persist($field4);

        $field5 = new Group('Groupe designer', array('ROLE_DESIGNER'));
        $field5->setEnabled(true);
        $field5->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $manager->persist($field5);

        $field6 = new Group('Groupe Editorial', array('ROLE_EDITOR', 'ROLE_MODERATOR'));
        $field6->setEnabled(true);
        $field6->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $manager->persist($field6);

        $manager->flush();

        $this->addReference('group-subscriber', $field0);
        $this->addReference('group-member', $field0_bis);
        $this->addReference('group-user', $field1);
        $this->addReference('group-admin', $field2);
        $this->addReference('group-superadmin', $field3);
        $this->addReference('group-manager', $field4);
        $this->addReference('group-designer', $field5);
        $this->addReference('group-editorial', $field6);
    }

    /**
     * Retrieve the order number of current fixture
     *
     * @return integer
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2011-12-28
     */
    public function getOrder()
    {
        // The order in which fixtures will be loaded
        return 1;
    }
}
