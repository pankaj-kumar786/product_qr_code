<?php

namespace Drupal\product_qr_code\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\product_qr_code\ProductQrCodeGenerator;

/**
 * Custom block for browser geolocation.
 *
 * @Block(
 *   id = "product_qr_code_block",
 *   admin_label = @Translation("Product QR Code"),
 *   category = @Translation("Product QR Code"),
 * )
 */
class ProductQrCodeBlock extends BlockBase implements ContainerFactoryPluginInterface {


  /**
   * The product_qr_code generator service.
   *
   * @var \Drupal\product_qr_code\ProductQrCodeGenerator
   */
  protected $productQrCodeGenerator;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new Copyright Block instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\product_qr_code\ProductQrCodeGenerator $product_qr_code_generator
   *   The product qr code generator service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ProductQrCodeGenerator $product_qr_code_generator, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->productQrCodeGenerator = $product_qr_code_generator;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('product_qr_code.product_qr_code_helper'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    // Get the current Node object.
    $node = $this->routeMatch->getParameter('node');
    // Get App Purchase Link object.
    $app_purchase_link = $node->get('field_app_purchase_link')->getValue();
    // Get App Purchase Link URI.
    $app_purchase_link_uri = $app_purchase_link[0]['uri'];
    if ($node instanceof NodeInterface && $node->bundle() === 'product') {
      $qr_code_image = $this->productQrCodeGenerator->generateProductQrCode($app_purchase_link_uri)->getDataUri();
      $build['product_qr_code'] = [
        '#type' => 'inline_template',
        '#template' => '<img src="{{image_path}}">',
        '#context' => [
          'image_path' => $qr_code_image,
        ],
      ];
    }
    return $build;
  }

  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = $this->routeMatch->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

}
