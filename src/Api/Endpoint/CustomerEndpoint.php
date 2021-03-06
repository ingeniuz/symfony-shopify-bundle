<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;

class CustomerEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @param array $links
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function findAll(array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customers.json', $query);
        $response = $this->sendPaged($request, 'customers', $links);
        return $this->createCollection($response);
    }

    /**
     * @param int $customerId
     * @param array $fields
     * @param array $links
     * @return array|GenericResource[]
     */
    public function findOrdersForCustomer($customerId, array $fields = array(), array &$links = array())
    {
        $params = $fields ? array('fields' => implode(',', $fields)) : array();
        $request = new GetJson('/admin/api/' . $this->version . '/customers/' . $customerId . '.json', $params);
        $response = $this->sendPaged($request, 'customers', $links);
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @param array $links
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function search(array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customers/search.json', $query);
        $response = $this->sendPaged($request, 'customers', $links);
        return $this->createCollection($response);
    }

    /**
     * @return int
     */
    public function countAll()
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customers/count.json');
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $customerId
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function findOne($customerId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customers/' . $customerId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('customer'));
    }

    /**
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $customer
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create(GenericResource $customer)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/customers.json', array('customer' => $customer->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('customer'));
    }

    /**
     * @param int $customerId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $customer
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($customerId, GenericResource $customer)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/customers/' . $customerId . '.json', array('customer' => $customer->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('customer'));
    }

    /**
     * @param int $customerId
     */
    public function delete($customerId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/customers/' . $customerId . '.json');
        $this->send($request);
    }
}
