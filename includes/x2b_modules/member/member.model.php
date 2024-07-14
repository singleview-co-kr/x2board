<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */
/**
 * @class  memberModel
 * @author XEHub (developers@xpressengine.com)
 * @brief Model class of the member module
 */
namespace X2board\Includes\Modules\Member;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Member\\memberModel' ) ) {

	class memberModel extends member {
		/**
		 * @brief Initialization
		 */
		// public function init() {}

		/**
		 * @brief Create a hash of plain text password
		 * hashPassword($password_text, $algorithm = null)
		 * @param string $password_text The password to hash
		 * @param string $algorithm The algorithm to use (optional, only set this when you want to use a non-default algorithm)
		 * @return string
		 */
		public function hash_password( $password_text, $algorithm = null ) {
			$o_password = new \X2board\Includes\Classes\Security\Password();
			$s_pw       = $o_password->create_hash( $password_text, $algorithm );
			unset( $o_password );
			return $s_pw;
		}

		/**
		 * @brief Compare plain text password to the password saved in DB
		 * isValidPassword($hashed_password, $password_text, $member_srl=null)
		 * @param string $hashed_password The hash that was saved in DB
		 * @param string $password_text The password to check
		 * @param int    $member_srl Set this to member_srl when comparing a member's password (optional)
		 * @return bool
		 */
		public function validate_password( $s_hashed_password, $s_password_text ) {
			// , $member_srl=null
			// False if no password in entered
			if ( ! $s_password_text ) {
				return false;
			}
			// Check the password
			$o_password = new \X2board\Includes\Classes\Security\Password();
			$match      = $o_password->check_password( $s_password_text, $s_hashed_password );
			unset( $o_password );
			return $match; // bool
		}
	}
}
