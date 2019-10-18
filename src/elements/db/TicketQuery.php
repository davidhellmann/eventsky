<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements\db;

use Craft;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use fredmansky\eventsky\elements\Ticket;

/**
 * TicketQuery represents a SELECT SQL statement for events in a way that is independent of DBMS.
 *
// * @property string|string[]|TicketType $type The handle(s) of the entry type(s) that resulting entries must have.
 * @author Fredmansky
 * @since 3.0
 * @supports-structure-params
 * @supports-site-params
 * @supports-enabledforsite-param
 * @supports-title-param
 * @supports-slug-param
 * @supports-status-param
 * @supports-uri-param
 * @replace {element} ticket
 * @replace {elements} tickets
 * @replace {twig-method} craft.tickets()
 * @replace {myElement} Ticket
 * @replace {element-class} \fredmansky\eventsky\elements\Ticket
 */
class TicketQuery extends ElementQuery
{
    public $title;
    public $name;
    public $description;
    public $typeId;
    public $eventId;
    public $authorId;
    public $postDate;
    public $expiryDate;
    public $dateDeleted;

    public function name($value)
    {
        $this->name = $value;
        return $this;
    }

    public function description($value)
    {
        $this->description = $value;
        return $this;
    }

    public function __construct(string $elementType, array $config = [])
    {
      // Default status
      if (!isset($config['status'])) {
        $config['status'] = TICKET::STATUS_ENABLED;
      }

      parent::__construct($elementType, $config);
    }

    public function typeId($value)
    {
      $this->typeId = $value;
      return $this;
    }

    public function eventId($value)
    {
      $this->eventId = $value;
      return $this;
    }

    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('eventsky_tickets');

        // select the price column
        $this->query->select([
          'eventsky_tickets.typeId',
          'eventsky_tickets.eventId',
          'eventsky_tickets.name',
          'eventsky_tickets.description',
          'eventsky_tickets.startDate',
          'eventsky_tickets.endDate',
          'eventsky_tickets.postDate',
          'eventsky_tickets.expiryDate',
          'eventsky_tickets.dateDeleted',
        ]);

        if ($this->typeId) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.typeId', $this->typeId));
        }

        if ($this->eventId) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.eventId', $this->eventId));
        }

        if ($this->name) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.name', $this->name));
        }

        if ($this->description) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.description', $this->description));
        }

        if ($this->dateDeleted) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.dateDeleted', $this->dateDeleted));
        }
        return parent::beforePrepare();
    }
}
