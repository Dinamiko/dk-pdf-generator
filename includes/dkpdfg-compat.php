<?php
/**
 * Compatibility layer for DK PDF >= 2.1
 *
 * DK PDF 2.1 moved its global helper functions into container services
 * (Dinamiko\DKPDF\Container). This file re-creates the legacy functions
 * used by DK PDF Generator, only if DK PDF does not provide them itself
 * (DK PDF <= 2.0.x still does).
 *
 * Loaded on plugins_loaded (priority 20), after DK PDF has booted.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a DK PDF container service or null if not available
 *
 * @param string $id service id
 *
 * @return mixed|null
 */
function dkpdfg_dkpdf_service( $id ) {

	if ( ! class_exists( '\Dinamiko\DKPDF\Container' ) ) {
		return null;
	}

	try {
		$container = \Dinamiko\DKPDF\Container::container();

		return $container->has( $id ) ? $container->get( $id ) : null;
	} catch ( \Throwable $e ) {
		return null;
	}

}

if ( ! function_exists( 'dkpdf_get_template' ) ) {

	/**
	 * Returns rendered DK PDF template content (header, footer...)
	 *
	 * @param string template name
	 *
	 * @return string
	 */
	function dkpdf_get_template( $template_name ) {

		$renderer = dkpdfg_dkpdf_service( 'template.renderer' );

		if ( $renderer && method_exists( $renderer, 'get_template' ) ) {
			return $renderer->get_template( $template_name );
		}

		return '';

	}
}

if ( ! function_exists( 'dkpdf_get_post_types' ) ) {

	/**
	 * Returns array of post types
	 *
	 * @return array
	 */
	function dkpdf_get_post_types() {

		$helper = dkpdfg_dkpdf_service( 'core.helper' );

		if ( $helper && method_exists( $helper, 'get_post_types' ) ) {
			return $helper->get_post_types();
		}

		$custom_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
		$post_arr     = array( 'post' => 'post', 'page' => 'page', 'attachment' => 'attachment' );

		foreach ( $custom_types as $post_type ) {
			$post_arr[ $post_type ] = $post_type;
		}

		return apply_filters( 'dkpdf_posts_arr', $post_arr );

	}
}
