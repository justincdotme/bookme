<?php

namespace App\Core\Property;

class PropertySearch
{
    protected $type;
    protected $property;
    protected $results;
    protected $query;

    function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type = null)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param null $query
     * @return $this
     */
    public function search($query = null)
    {
        $this->query = $query;
        switch ($this->type) {
            case 'city-state':
                if (isset($query['city']) && isset($query['state'])) {
                    $this->results = $this->byCityState($query['city'], $query['state']);
                } else {
                    $this->results = $this->withoutQuery();
                }
                break;
            default:
                $this->results = $this->withoutQuery();
        }

        return $this;
    }

    /**
     * @param $city
     * @param $stateId
     * @return mixed
     */
    protected function byCityState($city, $stateId)
    {
        return $this->property->where([
            ['city', strtolower($city)],
            ['state_id', $stateId]
        ]);
    }

    /**
     * @return Property
     */
    protected function withoutQuery()
    {
        return $this->property;
    }

    /**
     * @param int $paginateCount - Defaults to 10
     * @return mixed
     */
    public function getResults($paginateCount = 10)
    {
        return $this->results->paginate($paginateCount)->withPath("/properties/search{$this->getQueryString()}");
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        if (empty($this->query)) {
            return null;
        }

        unset($this->query['page']);
        $queryString = http_build_query($this->query);
        return "?{$queryString}";
    }
}