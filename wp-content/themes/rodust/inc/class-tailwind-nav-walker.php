<?php
/**
 * Tailwind CSS Nav Walker para WordPress
 * Equivalente ao Bootstrap Nav Walker, mas para Tailwind CSS
 */

class Tailwind_Nav_Walker extends Walker_Nav_Menu {
    
    // Início da lista
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"nav-submenu\">\n";
    }
    
    // Fim da lista
    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    // Início do item
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Classes específicas do Tailwind
        $li_classes = array();
        
        // Se tem submenu
        if (in_array('menu-item-has-children', $classes)) {
            $li_classes[] = 'relative group';
        }
        
        // Se é item atual
        if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
            $li_classes[] = 'current-menu-item';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($li_classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        // Link do menu
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        // Classes do link
        $link_classes = array();
        
        if ($depth === 0) {
            // Menu principal
            $link_classes[] = 'block py-2 px-3 text-gray-300 hover:text-white font-medium transition-all duration-300 rounded-md';
            if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
                $link_classes[] = '!text-white menu-active-glow';
            }
        } else {
            // Submenu
            $link_classes[] = 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900';
        }
        
        // Se tem submenu, adiciona seta
        $has_children = in_array('menu-item-has-children', $classes);
        $dropdown_icon = '';
        if ($has_children && $depth === 0) {
            $dropdown_icon = ' <svg class="ml-1 h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
        }
        
        $link_class_attr = ' class="' . implode(' ', $link_classes) . '"';
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . $link_class_attr . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= $dropdown_icon . '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    // Fim do item
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}