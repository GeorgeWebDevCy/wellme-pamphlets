<?php
/**
 * Register all hooks for the plugin.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_Loader {

    /**
     * @var array Actions registered with WordPress.
     */
    protected $actions = [];

    /**
     * @var array Filters registered with WordPress.
     */
    protected $filters = [];

    /**
     * Add a new action to the collection.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new filter to the collection.
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
        $hooks[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
        return $hooks;
    }

    /**
     * Register all hooks with WordPress.
     */
    public function run() {
        foreach ( $this->filters as $hook ) {
            add_filter(
                $hook['hook'],
                [ $hook['component'], $hook['callback'] ],
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ( $this->actions as $hook ) {
            add_action(
                $hook['hook'],
                [ $hook['component'], $hook['callback'] ],
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}
