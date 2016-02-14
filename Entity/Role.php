<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    Entity
 * @subpackage Model
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Sfynx\AuthBundle\Repository\RoleRepository;


/**
 * Sfynx\AuthBundle\Entity\Role
 *
 * @ORM\Table(name="fos_role")
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Repository\RoleRepository")
 * @ORM\HasLifecycleCallbacks() 
 * 
 * @category   Auth
 * @package    Entity
 * @subpackage Model
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class Role
{
    /**
     * @var bigint $id
     * 
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="Sfynx\AuthBundle\Entity\Ressource")
     * @ORM\JoinTable(name="fos_role_ressource",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ressource_id", referencedColumnName="id")}
     * )
     */
    protected $accessControl;    
    
    /**
     * @ORM\Column(type="string",length=55, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3, minMessage = "Le label doit avoir au moins {{ limit }} caractères")
     */
    protected $label;    
    
    /**
     * @ORM\Column(type="string",length=25, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min = 8, max = 25, minMessage = "Le nom doit avoir au moins {{ limit }} caractères", maxMessage = "Le nom doit avoir au plus {{ limit }} caractères")
     */
    protected $name;    
    
    /**
     * @var text $comment
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message = "You must enter a comment")
     * @Assert\Length(min = 25, minMessage = "Le commentaire doit avoir au moins {{ limit }} caractères")
     */
    protected $comment;    

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $heritage;
    
    /**
     * @var string $route_name
     * 
     * @ORM\Column(name="route_login", type="string", nullable=true)
     * @Assert\Length(min = 3, minMessage = "Le route name doit avoir au moins {{ limit }} caractères")
     * @Assert\Blank
     */
    protected $route_login;
    
    /**
     * @var string $route_name
     *
     * @ORM\Column(name="route_logout", type="string", nullable=true)
     * @Assert\Length(min = 3, minMessage = "Le route name doit avoir au moins {{ limit }} caractères")
     * @Assert\Blank
     */
    protected $route_logout;    

    /**
     * @var integer $layout
     * 
     * @ORM\ManyToOne(targetEntity="Sfynx\AuthBundle\Entity\Layout", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id", nullable=true)
     */
    protected $layout;    
    
    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;   
    

    public function __construct()
    {
        $this->accessControl = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     *
     * This method is used when you want to convert to string an object of
     * this Entity
     * ex: $value = (string)$entity;
     *
     */
    public function __toString() {
        return (string) $this->label;
    }    
    
    /**
     * Get id
     *
     * @return bigint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = strtoupper($name);
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set heritage
     *
     * @param array $heritage
     */
    public function setHeritage( array $heritage)
    {
        $this->heritage = array();
        foreach ($heritage as $role) {
            $this->addRoleInHeritage($role);
        } 
    }

    /**
     * Get heritage
     *
     * @return array
     */
    public function getHeritage()
    {
        $roles = $this->heritage;    
        // we need to make sure to have at least one role
        $roles[] = RoleRepository::ShowDefaultRole();
    
        return array_unique($roles);
    }
    
    /**
     * Adds a role heritatge to the role.
     *
     * @param string $role
     */
    public function addRoleInHeritage($role)
    {
        $role = strtoupper($role);    
        if (!in_array($role, $this->heritage, true)) {
            $this->heritage[] = $role;
        }
    }    

    /**
     * Add accessControl
     *
     * @param \Sfynx\AuthBundle\Entity\Ressource $accessControl
     */
    public function addRessource(\Sfynx\AuthBundle\Entity\Ressource $accessControl)
    {
        $this->accessControl[] = $accessControl;
    }

    /**
     * Get accessControl
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getAccessControl()
    {
        return $this->accessControl;
    }
    
    /**
     * Set route_name
     *
     * @param string $routeName
     */
    public function setRouteLogin($routeName)
    {
        $this->route_login = $routeName;
    }

    /**
     * Get route_name
     *
     * @return string 
     */
    public function getRouteLogin()
    {
        return $this->route_login;
    }
    
    /**
     * Set route_name
     *
     * @param string $routeName
     */
    public function setRouteLogout($routeName)
    {
    	$this->route_logout = $routeName;
    }
    
    /**
     * Get route_name
     *
     * @return string
     */
    public function getRouteLogout()
    {
    	return $this->route_logout;
    }    
    
    /**
     * Set layout
     *
     * @param \Sfynx\AuthBundle\Entity\Layout    $layout
     */
    public function setLayout(\Sfynx\AuthBundle\Entity\Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get layout
     *
     * @return \Sfynx\AuthBundle\Entity\Layout 
     */
    public function getLayout()
    {
        return $this->layout;
    }   

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
