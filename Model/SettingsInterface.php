<?php
namespace JustinKase\CanadaPostRates\Model;

/**
 * Interface SettingsInterface
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model
 */
interface SettingsInterface
{
    /**
     * Sprint template for the current module configs.
     */
    const CONFIG_PATH = 'carriers/canadapost/%s';

    // ADMIN CONFIGS IDS
    /**
     * The title of the method. Can be changed in the admin.
     */
    const TITLE = 'title';

    /**
     * Customer number from Canada Post.
     */
    const CUSTOMER_NUMBER = 'customer_number';
}
