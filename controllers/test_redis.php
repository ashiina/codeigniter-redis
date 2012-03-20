<?php 

/**
 * Small test suite for the Redis client library
 *
 * @see /application/libraries/Redis.php
 */
if (! defined('BASEPATH')) exit('No direct script access');

class Test_redis extends CI_Controller {
	
	function index() {
		
		$this->load->spark('redis/dev');
		$this->load->library('unit_test');
		
		// Make sure we're running in strict test mode
		$this->unit->use_strict(TRUE);
		
		// Generic command
		$this->unit->run($this->redis->command('PING'), 'PONG', 'Generic command (PING!)');
		
		// Overloading
		$this->unit->run($this->redis->hmset('myhash field1 "Hello" field2 "World"'), 'OK', 'Overloading (__call())');
		$this->unit->run($this->redis->hget('myhash field1'), '"Hello"', 'Overloading (__call())');
		$this->unit->run($this->redis->del('myhash'), 1 , 'Overloading (__call())');
		
		// SET
		$this->unit->run($this->redis->set('key', 'value'), 'OK', 'Set a key with a value');
		
		// GET
		$this->unit->run($this->redis->get('key'), 'value', 'Get the value of a set key');
		
		// DEL
		$this->unit->run($this->redis->del('key'),  1, 'Delete a set key');
		$this->unit->run($this->redis->del('key'),  0, 'Delete a unset key');
		$this->redis->set('key', 'value');
		$this->unit->run($this->redis->del('key key2'),  1, 'Delete a keys (space)');
		$this->redis->set('key', 'value');
		$this->unit->run($this->redis->del('key, key2'),  1, 'Delete a keys (comma)');
		$this->redis->set('key', 'value');
		$this->unit->run($this->redis->del(array('key', 'key2')),  1, 'Delete keys (array)');
		$this->redis->set('key', 'value');
		$this->unit->run($this->redis->del('key, key2'),  1, 'Delete a set and unset key');
		
		// KEYS
		$this->redis->set('key', 'value');
		$this->redis->set('key2', 'value');
		$this->unit->run($this->redis->keys('key*'),  array('key2', 'key'), 'Get all keys matching "key"');
		$this->redis->del('key key2');

		// SADD
		$this->unit->run($this->redis->sadd('test.set.1', 'val'), 1, 'Add member to set');

		// SREM
		$this->unit->run($this->redis->srem('test.set.1', 'val'), 1, 'Delete member from set');

		$this->redis->sadd('test.set.1', 'val1');
		$this->redis->sadd('test.set.1', 'val2');
		$this->redis->sadd('test.set.1', 'val3');

		// SMEMBERS
		$this->unit->run($this->redis->smembers('test.set.1'), array('val1','val2','val3'), 'Get all members of set "test.set.1"');

		// SCARD
		$this->unit->run($this->redis->scard('test.set.1'), 3, 'Get number of members of set "test.set.1"');

		// SISMEMBER
		$this->unit->run($this->redis->sismember('test.set.1', 'val4'), 0, 'Check if "val4" is a member of set "test.set.1"');
		$this->unit->run($this->redis->sismember('test.set.1', 'val2'), 1, 'Check if "val2" is a member of set "test.set.1"');

		// Display all results
		echo $this->unit->report();
		
	}

}
