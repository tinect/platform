<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Api\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\RepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\FieldException\WriteStackException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

class SyncController extends AbstractController
{
    public const ACTION_UPSERT = 'upsert';
    public const ACTION_DELETE = 'delete';

    /**
     * @var DefinitionRegistry
     */
    protected $registry;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(DefinitionRegistry $registry, ContainerInterface $container, Serializer $serializer)
    {
        $this->registry = $registry;
        $this->container = $container;
        $this->serializer = $serializer;
    }

    /**
     * @Route("api/_action/v{version}/sync/", name="api.action.sync", methods={"POST"})
     *
     * @param Request $request
     * @param Context $context
     *
     * @return Response
     */
    public function sync(Request $request, Context $context): Response
    {
        $payload = $this->serializer->decode($request->getContent(), 'json');

        $errors = $result = [];

        foreach ($payload as $operation) {
            $action = $operation['action'];
            $entity = $operation['entity'];

            $definition = $this->registry->get($entity);

            /** @var RepositoryInterface $repository */
            $repository = $this->container->get($definition::getEntityName() . '.repository');

            switch ($action) {
                case self::ACTION_DELETE:
                    /** @var EntityWrittenEvent $event */
                    $generic = $repository->delete([$operation['payload']], $context);

                    $errors = array_merge($errors, $generic->getErrors());

                    break;

                case self::ACTION_UPSERT:
                    try {
                        /** @var EntityWrittenEvent $event */
                        $generic = $repository->upsert(
                            [$operation['payload']],
                            $context
                        );

                        $events = $generic->getEvents();

                        foreach ($events as $event) {
                            /** @var string $eventDefinition */
                            $eventDefinition = $event->getDefinition();

                            if (array_key_exists($eventDefinition, $result)) {
                                $result[$eventDefinition]['ids'] = array_merge(
                                    $result[$eventDefinition]['ids'],
                                    $event->getIds()
                                );
                            } else {
                                $result[$eventDefinition] = [
                                    'definition' => $eventDefinition,
                                    'ids' => $event->getIds(),
                                ];
                            }

                            $errors = array_merge($errors, $event->getErrors());
                        }
                    } catch (WriteStackException $exception) {
                        $errors = array_merge($errors, $exception->toArray());
                    }

                    break;
            }
        }

        $result = array_values($result);

        $response = [
            'data' => $result,
            'errors' => $errors,
        ];

        return new JsonResponse($response);
    }
}
