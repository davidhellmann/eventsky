<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\models\TicketType;

class TicketService extends Component
{
//    public const EVENT_BEFORE_SAVE_TICKET = 'beforeSaveTicket';

    /** @var array */
    private $tickets;

    public function init()
    {
        parent::init();
    }

    public function getTicketById(int $id): ?ElementInterface
    {
        if (!$id) {
            return null;
        }

        return Craft::$app->getElements()->getElementById($id, Ticket::class);
    }

    public function getTicketsByType(TicketType $ticketType): array
    {
        $results = Ticket::find()
            ->typeId($ticketType->id)
            ->all();

        $tickets = array_map(function($result) {
            return new Ticket($result);
        }, $results);

        return $tickets;
    }

    public function deleteTicketById(int $id): bool
    {
        $ticket = $this->getTicketById($id);

        if (!$ticket) {
            return false;
        }

        return $this->deleteTicket($ticket);
    }

    public function deleteTicket(Ticket $ticket): bool
    {
//        $transaction = Craft::$app->getDb()->beginTransaction();
//        try {
//            Craft::$app->getDb()->createCommand()
//                ->softDelete(Table::TICKETS, ['id' => $ticket->id])
//                ->execute();
//
//            $transaction->commit();
//        } catch (\Throwable $e) {
//            $transaction->rollBack();
//            throw $e;
//        }

        $elementsService = Craft::$app->getElements();
        $elementsService->deleteElement($ticket);

        // Clear caches
        $this->tickets = null;

        return true;
    }
}
