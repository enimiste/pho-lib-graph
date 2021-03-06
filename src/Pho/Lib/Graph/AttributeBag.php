<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Lib\Graph;

/**
 * Holds edge and node attributes
 * 
 * All graph entities may hold attributes. AttributeBag class
 * is a common attribute of both edges and nodes.
 * 
 * ```
 * $node = new Node();
 * $node->attributes()->color = "red";
 * if(isset($node->attributes()->color))
 *   echo $node->attributes()->color; // prints "red"
 * unset($this->attributes()->color);
 * echo $this->attributes()->color; // doesn't print
 * ```
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class AttributeBag implements \SplSubject
{

    /**
     * Holds the attributes of a node in an array
     *
     * @var array
     */
    private $bag = [];

    /**
     * The entity (node or graph) that this bag belongs to.
     *
     * @var EntityInterface
     */
    private $owner;

    /**
     * The observers of this object. 
     * Normally just the owner.
     *
     * @var array
     */
    private $observers = array();

    /**
     * Constructor.
     *
     * Parameter optional.
     * 
     * @param array $bag Initial bag. Defaults to an empty array.
     */
    public function __construct(EntityInterface $owner, array $bag = []) 
    {
        $this->owner = $owner;
        $this->attach($this->owner);
        if(count($bag)>0) {
            $this->bag = $bag;
        }
    }

    /**
     * Adds a new observer to the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function attach(\SplObserver $observer): void 
    {
        $this->observers[] = $observer;
    }
    
    /**
     * Removes an observer from the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function detach(\SplObserver $observer): void 
    {
        $key = array_search($observer, $this->observers, true);
        if($key) {
            unset($this->observers[$key]);
        }
    }

    /**
     * Notifies observers about a change
     * 
     * @return void
     */
    public function notify(): void
    {
        foreach ($this->observers as $value) {
            $value->update($this);
        }
    }

    /**
     * Retrieves the bag in array format
     * 
     * Useful for serialization/unserialization.
     *
     * @return array The object in pure array key/value pair form.
     */
    public function toArray(): array
    {
        return $this->bag;
    }

    /**
     * @internal
     * Fetches value
     *
     * @param string $attribute
     * 
     * @return mixed
     */
    public function __get(string $attribute)
    {
        if(!isset($this->$attribute)) {
            return null;
        }
        return $this->bag[$attribute];
    }

    /**
     * @internal
     * Checks if key exists
     *
     * @param string $attribute
     * 
     * @return bool
     */
    public function __isset(string $attribute): bool
    {
        return isset($this->bag[$attribute]);
    }

    /**
     * @internal
     * Sets up a key/value pair
     *
     * @param string            $attribute
     * @param string|bool|array $value
     * 
     * @return void
     */
    public function __set(string $attribute, /*string|bool|array*/ $value): void
    {
        $this->bag[$attribute] = $value;
        $this->notify();
    }

    /**
     * @internal
     * Removes a key/value pair
     *
     * @param string $attribute
     * 
     * @return void
     */
    public function __unset(string $attribute): void
    {
        unset($this->bag[$attribute]);
        $this->notify();
    }

}