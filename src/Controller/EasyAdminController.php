<?php

namespace App\Controller;

use App\Controller\Crud\UserCrudController;
use App\Entity\User;
use App\Entity\User\Artist;
use App\Entity\User\Customer;
use App\Entity\User\Merchant;
use Base\Annotations\Annotation\Iconize;
use Base\Backend\Config\Actions;
use Base\Backend\Config\WidgetItem;
use Base\Controller\Backend\AbstractDashboardController;
use Base\Controller\Backend\Crud\Layout\Attribute\Adapter\Common\AbstractAdapterCrudController;
use Base\Entity\Layout\Attribute\Adapter\Common\AbstractActionAdapter;
use Base\Entity\Layout\Attribute\Adapter\Common\AbstractAdapter;
use Base\Entity\Layout\Attribute\Adapter\Common\AbstractRuleAdapter;
use Base\Entity\Layout\Attribute\Adapter\Common\AbstractScopeAdapter;
use Base\Entity\Layout\Attribute\Adapter\HyperpatternAdapter;
use Base\Entity\Layout\Attribute\Hyperlink;
use Base\Service\Model\LinkableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action as EaAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EasyAdminController extends AbstractDashboardController
{
    public function configureMenuItems(): iterable
    {
        return [];
    }

    /**
     * @Route("/", name="app_index")
     * @Route("/backoffice", name="app_backoffice")
     *
     * @Iconize({"fas fa-fw fa-toolbox", "fas fa-fw fa-home"})
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureWidgetItems(array $widgets = []): array
    {
        $widgets = [];

        $widgets = parent::addSectionWidgetItem($widgets, WidgetItem::section('ACCOUNTS', null, 2));
        $widgets = parent::addWidgetItem($widgets, 'ACCOUNTS', [
            WidgetItem::linkToCrud(User::class),
        ]);

        $widgets = $this->addSectionWidgetItem($widgets, WidgetItem::section('ATTRIBUTES', null, 1));
        $widgets = $this->addWidgetItem($widgets, "ATTRIBUTES", [
            WidgetItem::linkToUrl(AbstractAdapter::class, AbstractAdapter::__iconizeStatic()[0], $this->adminUrlGenerator->unsetAll()
                ->setController(AbstractAdapterCrudController::class)
                ->setAction(\Base\Backend\Config\Action::INDEX)
                ->set("filters[class][comparison]", "=")
                ->set("filters[class][value]", "abstract_adapter")->generateUrl()),
        ]);

        return $widgets;
    }
}
