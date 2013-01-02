<?php
/**
 * Copyright (c) 2012 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OC\Files\Cache;

/**
 * Provide read only support for the old filecache
 */
class Legacy {
	private $user;

	public function __construct($user) {
		$this->user = $user;
	}

	function getCount() {
		$query = \OC_DB::prepare('SELECT COUNT(`id`) AS `count` FROM `*PREFIX*fscache` WHERE `user` = ?');
		$result = $query->execute(array($this->user));
		if ($row = $result->fetchRow()) {
			return $row['count'];
		} else {
			return 0;
		}
	}

	/**
	 * check if a legacy cache is present and holds items
	 *
	 * @return bool
	 */
	function hasItems() {
		try {
			$query = \OC_DB::prepare('SELECT `id` FROM `*PREFIX*fscache` WHERE `user` = ? LIMIT 1');
		} catch (\Exception $e) {
			return false;
		}
		try {
			$result = $query->execute(array($this->user));
		} catch (\Exception $e) {
			return false;
		}
		return (bool)$result->fetchRow();
	}

	/**
	 * @param string|int $path
	 * @return array
	 */
	function get($path) {
		if (is_numeric($path)) {
			$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*fscache` WHERE `id` = ?');
		} else {
			$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*fscache` WHERE `path` = ?');
		}
		$result = $query->execute(array($path));
		return $result->fetchRow();
	}

	/**
	 * @param int $id
	 * @return array
	 */
	function getChildren($id) {
		$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*fscache` WHERE `parent` = ?');
		$result = $query->execute(array($id));
		return $result->fetchAll();
	}
}
