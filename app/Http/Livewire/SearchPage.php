<?php

namespace App\Http\Livewire;

use App\Models\Search;
use Livewire\Component;
use Goutte\Client;

class SearchPage extends Component
{
    public $categories = [1 => 'TV', 2 => 'Celular', 3 => 'Geladeira'];
    public $origins = [1 => 'ml', 2 => 'bp', 3 => 'both'];
    public $category = 1;
    public $origin = 1;
    public $description = null;
    private $url_bp_geladeira = "https://www.buscape.com.br/search?page=1&hierarchicalMenu%5BhierarchicalCategories.lvl0%5D=Eletrodom%C3%A9sticos%20%3E%20Geladeira&configure%5BruleContexts%5D%5B0%5D=search_page&configure%5BhitsPerPage%5D=36&configure%5BclickAnalytics%5D=true&configure%5BanalyticsTags%5D%5B0%5D=undefined&configure%5BanalyticsTags%5D%5B1%5D=undefined&configure%5BanalyticsTags%5D%5B2%5D=page_type%3Asearch_page&configure%5BanalyticsTags%5D%5B3%5D=brand%3Abuscape&configure%5BuserToken%5D=90caf588-d127-42db-9f63-278059a1ded8&q=";
    private $url_bp_celular = "https://www.buscape.com.br/search?page=1&hierarchicalMenu%5BhierarchicalCategories.lvl0%5D=Celulares%20e%20Telefones%20%3E%20Celular%20e%20Smartphone&configure%5BruleContexts%5D%5B0%5D=search_page&configure%5BhitsPerPage%5D=36&configure%5BclickAnalytics%5D=true&configure%5BanalyticsTags%5D%5B0%5D=undefined&configure%5BanalyticsTags%5D%5B1%5D=undefined&configure%5BanalyticsTags%5D%5B2%5D=page_type%3Asearch_page&configure%5BanalyticsTags%5D%5B3%5D=brand%3Abuscape&configure%5BuserToken%5D=90caf588-d127-42db-9f63-278059a1ded8&q=";
    private $url_bp_tv = "https://www.buscape.com.br/search?page=1&hierarchicalMenu%5BhierarchicalCategories.lvl0%5D=TV%20e%20Eletr%C3%B4nicos%20%3E%20TV&configure%5BruleContexts%5D%5B0%5D=search_page&configure%5BhitsPerPage%5D=36&configure%5BclickAnalytics%5D=true&configure%5BanalyticsTags%5D%5B0%5D=undefined&configure%5BanalyticsTags%5D%5B1%5D=undefined&configure%5BanalyticsTags%5D%5B2%5D=page_type%3Asearch_page&configure%5BanalyticsTags%5D%5B3%5D=brand%3Abuscape&configure%5BuserToken%5D=90caf588-d127-42db-9f63-278059a1ded8&q=";
    private $url_ml_geladeira = "https://lista.mercadolivre.com.br/geladeiras-e-freezers/";
    private $url_ml_celular = "https://celulares.mercadolivre.com.br/";
    private $url_ml_tv = "https://eletronicos.mercadolivre.com.br/televisores/";
    public $products = array();
    public function render($products = null)
    {
        if (!$products) {
            $products = array();
        };

        return view('livewire.search-page', [
            'products' => $products,
        ]);
    }

    public function search()
    {
        $client = new Client();
        $ml_products = array();
        $bp_products = array();
        $this->products = array();
        $searches = Search::where('origin', $this->origin)->where('category', $this->category)->first();
        if ($searches && !$this->description) {
            $this->products = json_decode($searches->products, true);
        } else {
            if ($this->origin == 1 || $this->origin == 3) {
                switch ($this->category) {
                    case 1:
                        $ml_products = $client->request('GET', $this->url_ml_tv . $this->description);
                        break;
                    case 2:
                        $ml_products = $client->request('GET', $this->url_ml_celular . $this->description);
                        break;
                    default:
                        $ml_products = $client->request('GET', $this->url_ml_geladeira . $this->description);
                        break;
                }
                $ml_products->filter('.ui-search-layout__item')->each(function ($node) {
                    $product['price'] = $node->filter('.price-tag-fraction')->text() . ",00";
                    $product['description'] = $node->filter('.ui-search-item__title')->text();
                    $product['image'] = $node->filter('.slick-slide > img', 0)->attr('data-src');
                    $product['origin'] = 'ml';
                    $product['url'] = $node->filter('.ui-search-result__image > a')->attr('href');
                    array_push($this->products, $product);
                });
            }

            if ($this->origin == 2 || $this->origin == 3) {
                switch ($this->category) {
                    case 1:
                        $bp_products = $client->request('GET', $this->url_bp_tv . $this->description);
                        break;
                    case 2:
                        $bp_products = $client->request('GET', $this->url_bp_celular . $this->description);
                        break;
                    default:
                        $bp_products = $client->request('GET', $this->url_bp_geladeira . $this->description);
                        break;
                }
                $bp_products->filter('.card--prod')->each(function ($node) {
                    $product['price'] = $node->filter('.mainValue')->text() . $node->filter('.centsValue')->text();
                    $product['description'] = $node->filter('.name')->text();
                    $product['image'] = $node->filter('.cardImage > img', 0)->attr('src');
                    $product['origin'] = 'bp';
                    $product['url'] = "https://www.buscape.com.br" . $node->filter('.card > a')->attr('href');
                    array_push($this->products, $product);
                });
            }
            if (!$this->description) {
                $search = Search::create([
                    'origin' => $this->origin,
                    'category' => $this->category,
                    'products' => json_encode($this->products),
                ]);
            }
        }
    }

    public function updatedOrigin($origin)
    {
        $this->origin = $origin;
    }

    public function updatedCategory($category)
    {
        $this->category = $category;
    }
}
