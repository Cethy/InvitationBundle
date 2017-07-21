Cethyworks/InvitationBundle
===
Provides a way to secure routes behind a special "invitation code only" security firewall.

[![CircleCI](https://circleci.com/gh/Cethy/InvitationBundle/tree/master.svg?style=shield)](https://circleci.com/gh/Cethy/InvitationBundle/tree/master)

## Install

1\. Composer require

    $ composer require cethyworks/invitation-bundle 

2\. Register bundles

    // AppKernel.php
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                // ...
                new Cethyworks\InvitationBundle\CethyworksInvitationBundle(),
            ];
            // ...


## How to use
### Invitation codes `in memory`
1\. Update `security.yml` to add the invitation provider, invitations and firewall :

    security:
        # ...
        providers:
            in_memory_invitation_provider:
                invitation_memory:
                    invitations:
                        - { code: foo }
                        - { code: bar }
        # ...
        firewalls:
            # ...            
            invitation:
                pattern: ^/invite-only-url
                provider: in_memory_invitation_provider
                guard:
                    authenticator:
                        cethyworks_invitation.authenticator
                anonymous: false
            # ...
 
4\. Go to `/invite-only-url?code=foo`. That's it.


#### To use emails with in memory provider :

1\. Update `config.yml` :

    cethyworks_invitation:
        invitation_class: Cethyworks\InvitationBundle\Model\Invitation

2\. Update `security.yml`  :

    security:
            # ...
            providers:
                in_memory_invitation_provider:
                    invitation_memory:
                        invitations:
                            - { code: foo, email: foo@email.foo }
                            - { code: bar, email: bar@email.bar }
            # ...

### Invitation codes `in database`
1\. Extends `Cethyworks\InvitationBundle\Model\Invitation` to persit it :

    <?php
    namespace AppBundle\Entity;
    
    use Cethyworks\InvitationBundle\Model\GenerateCodeTrait;
    use Cethyworks\InvitationBundle\Model\Invitation as BaseInvitation;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * Invitation
     *
     * @ORM\Table()
     * @ORM\Entity()
     */
    class Invitation extends BaseInvitation
    {
        // optional, provides a shortcut to generate random code
        use GenerateCodeTrait;
        
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        
        /**
         * Invitation constructor.
         */
        public function __construct()
        {
            $this->generateCode();
        }
    }

2\. Update `config.yml` with the new `Invitation` class :

    cethyworks_invitation:
        invitation_class: AppBundle\Entity\Invitation

3\. Update `security.yml` to add the invitation provider and firewall :

    security:
        # ...
        providers:
            cethyworks_invitation_entity_provider:
                invitation_entity: ~
                    # entity_property: 'code'
                    # repository_method: 'findOneBy'
                    # manager_name: null
        # ...
        firewalls:
            # ...            
            invitation:
                pattern: ^/invite-only-url
                provider: cethyworks_invitation_entity_provider
                guard:
                    authenticator:
                        cethyworks_invitation.authenticator
                anonymous: false
            # ...
 
4\. Go to `/invite-only-url?code=some_code`. That's it.


### Todo
- handle failure (redirection ?)
- showcase repository
- plugins :
    - attach invitation to user
    - form to manually set invitation code
    - consumeable
    - send invitation
