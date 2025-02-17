<?php

namespace Webfactory\Bundle\PolyglotBundle\Tests\Functional;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use PHPUnit\Framework\TestCase;
use Webfactory\Bundle\PolyglotBundle\Doctrine\PolyglotListener;
use Webfactory\Bundle\PolyglotBundle\Doctrine\TranslatableStringType;
use Webfactory\Bundle\PolyglotBundle\Locale\DefaultLocaleProvider;
use Webfactory\Doctrine\ORMTestInfrastructure\ORMInfrastructure;
use Webfactory\Doctrine\ORMTestInfrastructure\Query;

abstract class FunctionalTestBase extends TestCase
{
    protected ORMInfrastructure $infrastructure;
    protected EntityManagerInterface $entityManager;
    protected DefaultLocaleProvider $defaultLocaleProvider;

    protected function setupOrmInfrastructure(array $classes): void
    {
        if (!Type::hasType(TranslatableStringType::NAME)) {
            Type::addType(TranslatableStringType::NAME, TranslatableStringType::class);
        }
        $this->infrastructure = ORMInfrastructure::createOnlyFor($classes, mappingDriver: new AttributeDriver([], true));
        $this->entityManager = $this->infrastructure->getEntityManager();
        $this->defaultLocaleProvider = new DefaultLocaleProvider('en_GB');

        $this->entityManager->getEventManager()->addEventSubscriber(
            new PolyglotListener($this->defaultLocaleProvider)
        );
    }

    /**
     * @return Query[]
     */
    protected function getQueries(): array
    {
        return $this->infrastructure->getQueries();
    }
}
