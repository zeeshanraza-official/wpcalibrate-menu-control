<?php
/**
 * GitHub Release Updater for seamless in-dashboard WordPress updates.
 *
 * @package WPCalibrate\MenuControl\Support
 */

declare(strict_types=1);

namespace WPCalibrate\MenuControl\Support;

/**
 * Class GitHubUpdater
 */
final class GitHubUpdater {

	private const TRANSIENT_KEY = 'wpcalibrate_menu_control_gh_release';
	private const TRANSIENT_EXPIRATION = 43200; // 12 hours.

	private string $repo;
	private string $plugin_file;
	private string $slug;
	private string $version;

	/**
	 * Constructor.
	 *
	 * @param string $repo GitHub repository in 'owner/repo' format.
	 * @param string $plugin_file Main plugin absolute file path.
	 * @param string $version Current installed plugin version.
	 */
	public function __construct( string $repo, string $plugin_file, string $version ) {
		$this->repo        = $repo;
		$this->plugin_file = $plugin_file;
		$this->slug        = dirname( plugin_basename( $plugin_file ) );
		$this->version     = $version;
	}

	/**
	 * Register updater hooks.
	 */
	public function register(): void {
		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_information' ), 20, 3 );
		add_filter( 'upgrader_post_install', array( $this, 'post_install' ), 10, 3 );
	}

	/**
	 * Check GitHub latest release against currently installed version.
	 *
	 * @param mixed $transient Transient object.
	 * @return mixed Filtered transient object.
	 */
	public function check_for_update( mixed $transient ): mixed {
		if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_latest_release();
		if ( empty( $release ) || empty( $release['tag_name'] ) ) {
			return $transient;
		}

		$remote_version = ltrim( (string) $release['tag_name'], 'v' );
		$plugin_basename = plugin_basename( $this->plugin_file );

		if ( version_compare( $remote_version, $this->version, '>' ) ) {
			$download_url = $this->extract_download_url( $release );

			$item = (object) array(
				'id'            => 'github.com/' . $this->repo,
				'slug'          => $this->slug,
				'plugin'        => $plugin_basename,
				'new_version'   => $remote_version,
				'url'           => $release['html_url'] ?? 'https://github.com/' . $this->repo,
				'package'       => $download_url,
				'tested'        => '7.1.3',
				'requires_php'  => '8.2',
				'requires'      => '6.8',
				'compatibility' => new \stdClass(),
			);

			$dark_icon = Helpers::get_branding_icon_url( 'dark' );
			if ( ! empty( $dark_icon ) ) {
				$item->icons = array(
					'1x'      => $dark_icon,
					'2x'      => $dark_icon,
					'default' => $dark_icon,
				);
			}

			$transient->response[ $plugin_basename ] = $item;
		} else {
			// Cleanly remove from response if already up to date.
			unset( $transient->response[ $plugin_basename ] );
			if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
				$transient->no_update = array();
			}
			$transient->no_update[ $plugin_basename ] = (object) array(
				'id'          => 'github.com/' . $this->repo,
				'slug'        => $this->slug,
				'plugin'      => $plugin_basename,
				'new_version' => $this->version,
				'url'         => 'https://github.com/' . $this->repo,
				'package'     => '',
			);
		}

		return $transient;
	}

	/**
	 * Provide plugin details for the modal thickbox in admin.
	 *
	 * @param mixed  $res Existing result.
	 * @param string $action Action name.
	 * @param mixed  $args Query args.
	 * @return mixed
	 */
	public function plugin_information( mixed $res, string $action, mixed $args ): mixed {
		if ( 'plugin_information' !== $action || ! is_object( $args ) || empty( $args->slug ) ) {
			return $res;
		}

		if ( $args->slug !== $this->slug ) {
			return $res;
		}

		$release = $this->get_latest_release();
		if ( empty( $release ) ) {
			return $res;
		}

		$remote_version = ltrim( (string) ( $release['tag_name'] ?? $this->version ), 'v' );
		$changelog      = ! empty( $release['body'] ) ? wp_kses_post( $release['body'] ) : 'Detailed release notes on GitHub.';

		$info = (object) array(
			'name'          => 'WPCalibrate Menu Control',
			'slug'          => $this->slug,
			'version'       => $remote_version,
			'author'        => '<a href="https://wpcalibrate.com">WPCalibrate</a>',
			'homepage'      => 'https://marketplace.wpcalibrate.com/',
			'requires'      => '6.8',
			'tested'        => '7.1.3',
			'requires_php'  => '8.2',
			'download_link' => $this->extract_download_url( $release ),
			'sections'      => array(
				'description' => 'Advanced frontend menu control: conditional visibility rules, icons, badges, subtitles, non-clickable headings, WooCommerce dynamic cart/account items, and lightweight mega menus.',
				'changelog'   => $changelog,
			),
		);

		return $info;
	}

	/**
	 * Post-install hook: rename extracted directory from GitHub repo-tag to the proper plugin slug.
	 *
	 * @param bool  $response True on success.
	 * @param array $hook_extra Extra upgrader arguments.
	 * @param array $result Result containing destination.
	 * @return array Result array.
	 */
	public function post_install( bool $response, array $hook_extra, array $result ): array {
		global $wp_filesystem;

		if ( ! $response || empty( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== plugin_basename( $this->plugin_file ) ) {
			return $result;
		}

		if ( empty( $result['destination'] ) || empty( $wp_filesystem ) ) {
			return $result;
		}

		$proper_destination = trailingslashit( WP_PLUGIN_DIR ) . $this->slug;

		if ( untrailingslashit( $result['destination'] ) !== untrailingslashit( $proper_destination ) ) {
			$wp_filesystem->move( $result['destination'], $proper_destination );
			$result['destination'] = $proper_destination;
		}

		return $result;
	}

	/**
	 * Fetch latest release from GitHub API with caching.
	 *
	 * @return array
	 */
	private function get_latest_release(): array {
		// Bypass cache if force-check query param is present on dashboard updates page.
		$force = isset( $_GET['force-check'] ) && '1' === (string) $_GET['force-check']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! $force ) {
			$cached = get_site_transient( self::TRANSIENT_KEY );
			if ( is_array( $cached ) && ! empty( $cached['tag_name'] ) ) {
				return $cached;
			}
		}

		$url = 'https://api.github.com/repos/' . $this->repo . '/releases/latest';

		$response = wp_remote_get(
			$url,
			array(
				'timeout'    => 10,
				'user-agent' => 'WPCalibrate-Menu-Control/' . $this->version . '; ' . home_url(),
				'headers'    => array(
					'Accept' => 'application/vnd.github.v3+json',
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
			return array();
		}

		set_site_transient( self::TRANSIENT_KEY, $data, self::TRANSIENT_EXPIRATION );

		return $data;
	}

	/**
	 * Extract zip asset URL from release payload, or fallback to zipball.
	 *
	 * @param array $release Release payload.
	 * @return string URL to zip package.
	 */
	private function extract_download_url( array $release ): string {
		if ( ! empty( $release['assets'] ) && is_array( $release['assets'] ) ) {
			foreach ( $release['assets'] as $asset ) {
				$name = strtolower( (string) ( $asset['name'] ?? '' ) );
				if ( str_ends_with( $name, '.zip' ) && ! empty( $asset['browser_download_url'] ) ) {
					return (string) $asset['browser_download_url'];
				}
			}
		}

		return (string) ( $release['zipball_url'] ?? '' );
	}
}
