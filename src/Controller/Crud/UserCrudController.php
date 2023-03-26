<?php

namespace App\Controller\Crud;

use App\Entity\User;
use Base\Backend\Config\Extension;
use Base\Controller\Backend\Crud\UserActionCrudController;
use Base\Entity\Layout\Attribute\Adapter\HyperpatternAdapter;
use Base\Field\ArrayField;
use Base\Field\AttributeField;
use Base\Field\AvatarField;

use Base\Field\PasswordField;
use Base\Field\RoleField;
use Base\Field\BooleanField;

use Base\Field\EmailField;
use Base\Security\LoginRestrictionInterface;
use Base\Service\Translator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\PropertyAccess\PropertyAccess;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class UserCrudController extends UserActionCrudController
{
    public static function getPreferredIcon(): ?string
    {
        return null;
    }

    public function configureExtensionWithResponseParameters(Extension $extension, KeyValueStore $responseParameters): Extension
    {
        if ($entity = $this->getEntity()) {
            $extension->setImage($entity->getAvatar());

            $userClass = "user.".mb_strtolower(camel2snake(class_basename($entity)));
            $entityLabel = $this->translator->transQuiet($userClass.".".Translator::NOUN_SINGULAR, [], Translator::DOMAIN_ENTITY);
            if ($entityLabel) {
                $extension->setTitle(mb_ucwords($entityLabel));
            }

            $entityLabel ??= $this->getCrud()->getAsDto()->getEntityLabelInSingular() ?? "";
            $entityLabel   = $entityLabel ? mb_ucwords($entityLabel) : "";

            $switchRole      = $this->router->getRouteFirewall()?->getSwitchUser()["role"] ?? null;
            $switchParameter = $this->router->getRouteFirewall()?->getSwitchUser()["parameter"] ?? "_switch_user";

            $impersonate = null;
            if ($switchRole && $this->isGranted($switchRole) && !is_instanceof($this->getEntityFqcn(), LoginRestrictionInterface::class)  && $this->getCrud()->getAsDto()->getCurrentAction() != "new") {
                $propertyAccessor =  PropertyAccess::createPropertyAccessor();
                if ($propertyAccessor->isReadable($entity, User::__DEFAULT_IDENTIFIER__)) {
                    $impersonate = '<a class="impersonate" href="?'.$switchParameter.'='.$propertyAccessor->getValue($entity, User::__DEFAULT_IDENTIFIER__).'"><i class="fa fa-fw fa-user-secret"></i></a>';
                }
            }

            if ($this->getCrud()->getAsDto()->getCurrentAction() == "new") {
                $extension->setTitle($entityLabel);
            } else {
                $extension->setTitle($entity.$impersonate);
                $extension->setText($entityLabel." #".$entity->getId()." | ".$this->translator->trans("crud.user.since", [$entity->getCreatedAt()->format("Y")], Translator::DOMAIN_BACKEND));
            }
        }

        return $extension;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('roles');
    }

    public function configureFields(string $pageName, ...$args): iterable
    {
        return parent::configureFields($pageName, function () {

            yield EmailField::new('email')->setColumns(12);
            yield AttributeField::new('hyperlinks')->setColumns(12)->setFilter(HyperpatternAdapter::class);

            yield ArrayField::new("array")->setColumns(2);
            yield ArrayField::new("array_associative")->useAssociativeKeys()->setColumns(2);

        }, $args);
    }
}
