<nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">Innovuratech</a>
      </div>
      <ul class="nav navbar-nav">
        <li class={{ Route::current()->uri() == 'clients' ? 'active' : '' }}><a href={{ route('clients.index') }}>Client Listing</a></li>
        <li class={{ Route::current()->uri() == 'clients/create' ? 'active' : '' }}><a href={{ route('clients.create') }}>Add Client</a></li>
      </ul>
    </div>
  </nav>
