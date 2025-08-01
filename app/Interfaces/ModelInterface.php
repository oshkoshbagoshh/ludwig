<?php

namespace App\Interfaces;

/**
 * Model Interface
 * 
 * Defines the contract for model classes.
 */
interface ModelInterface
{
    /**
     * Get the model ID
     * 
     * @return int|null The model ID
     */
    public function getId(): ?int;
    
    /**
     * Set the model ID
     * 
     * @param int $id The model ID
     * @return self
     */
    public function setId(int $id): self;
    
    /**
     * Get the model attributes
     * 
     * @return array The model attributes
     */
    public function getAttributes(): array;
    
    /**
     * Set the model attributes
     * 
     * @param array $attributes The model attributes
     * @return self
     */
    public function setAttributes(array $attributes): self;
    
    /**
     * Get a model attribute
     * 
     * @param string $name The attribute name
     * @param mixed $default The default value if the attribute doesn't exist
     * @return mixed The attribute value
     */
    public function getAttribute(string $name, mixed $default = null): mixed;
    
    /**
     * Set a model attribute
     * 
     * @param string $name The attribute name
     * @param mixed $value The attribute value
     * @return self
     */
    public function setAttribute(string $name, mixed $value): self;
    
    /**
     * Check if a model attribute exists
     * 
     * @param string $name The attribute name
     * @return bool True if the attribute exists, false otherwise
     */
    public function hasAttribute(string $name): bool;
    
    /**
     * Fill the model with attributes
     * 
     * @param array $attributes The attributes to fill
     * @return self
     */
    public function fill(array $attributes): self;
    
    /**
     * Convert the model to an array
     * 
     * @return array The model as an array
     */
    public function toArray(): array;
    
    /**
     * Save the model
     * 
     * @return bool True if the model was saved, false otherwise
     */
    public function save(): bool;
    
    /**
     * Delete the model
     * 
     * @return bool True if the model was deleted, false otherwise
     */
    public function delete(): bool;
    
    /**
     * Validate the model
     * 
     * @return array The validation errors or an empty array if valid
     */
    public function validate(): array;
}