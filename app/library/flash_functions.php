<?php

namespace QT\Flash;

/**
 * Updates session variables with options for the flash
 *
 * Use:  set_flash(['message' => 'That worked']);
 *
 * @param array   $options     - Array of options for the flash
 *        string  message      - message to display to user (can contain HTML)
 *        string  type         - One of 'alert','notice'
 *        boolean is_closeable - Is the flash message user closeable with a close button
 *        boolean is_persisent - does the message persist for the entire session - unless closed
 *        boolean display_for  - how many seconds to display the message before auto-closing (not-implemented)
 *
 * @return void
 */

function set_flash(array $options): void
{
    $defaults = [
        'type'          => 'notice',
        'is_closeable'  => true,
        'is_persistent' => false,
        'display_for'   => 5
    ];

    $flash = \array_merge($defaults, $options);

    if (empty($flash['message']))
    {
        throw new \Exception('Missing \'message\' option in set_flash()');
    }

    $_SESSION['flash'] = $flash;
}

/**
 * Clears the flash
 *
 * Use: clear_flash();
 *
 * @return void
 */

function clear_flash(): void
{
    if (isset($_SESSION['flash']))
    {
        unset($_SESSION['flash']);
    }
}

/**
 * Gets the flash
 *
 * Use: get_flash();
 *
 * @return array
 */

function get_flash(): ?array
{
    if (!isset($_SESSION['flash']))
    {
        return null;
    }

    return $_SESSION['flash'];
}

/**
 * Gets the CSS classes that are needed to show the flash
 *
 * Use:  get_classes($flash);
 *
 * @return array - array of strings
 */

function get_classes(array $flash): array
{
    $array = [$flash['type']];

    // if ($flash['is_closeable'])
    // {
    //     \array_push($array, 'closeable');
    // }

    return $array;
}
