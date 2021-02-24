    <div class="container mt-5 mb-5">
        <div class="p-2 bg-white px-4">
            <div class="text-center">
                <div class="align-items-center filters">
                    <h4>Filtrar Produtos</h4><span class="ml-2">{{count($products)}} itens</span>
                </div>
                <form method="POST" wire:submit.prevent="search">
                    @csrf
                    <div class="d-flex flex-row align-items-center filters">
                        <div class="form-group col-8">
                            <label for="description">Nome do Produto</label>
                            <input type="text" class="form-control" name="description" wire:model="description" id="description" wire:model="description" placeholder="Samsung LED">
                        </div>
                        <div class="form-group col-2">
                            <label for="origin">Origem</label>
                            <select class="form-control" wire:model="origin" id="origin">
                                <option value="3">Ambos</option>
                                <option value="2">Buscapé</option>
                                <option value="1">Mercado Livre</option>
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label for="category">Categoria</label>
                            <select class="form-control" wire:model="category" id="category">
                                <option value="2">Celulares</option>
                                <option value="3">Geladeiras</option>
                                <option value="1">TV's</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary col-12">Search</button>
                </form>
            </div>
        </div>
        <div class="row">
            @foreach ($products as $product)
            <div class="col-12 mt-2">
                <div class="p-4 bg-white">
                    <div class="d-flex flex-row">
                        <div class="text-center col-3"><img class="img-responsive" src={{ $product["image"] }} width="" height="220"></div>
                        <div class="d-flex flex-column align-items-center py-5 col-9">
                            <h5>{{$product['description']}}</h5>
                            @if($product['origin'] == 'ml')
                            <p>Mercado Livre</p>
                            @else
                            <p>Buscapé</p>
                            @endif
                            <h3>R$ {{$product['price']}}</h3>
                            <div class="text-center col-10 ml-4"><a href="{{$product['url']}}"><button class="btn btn-primary col-12">Acessar</button></a></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>