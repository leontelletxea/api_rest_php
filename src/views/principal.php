{% extends 'layout.html' %}

{% block content %}
<body style='background-color: rgb(153, 167,184);'>
    <div class="container w-70" style="margin-top:30px">
        <nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul id="main-nav" class="navbar-nav">
                    <li class="dropdown dropdown d-flex align-items-center">
                        <img src="." class="img-thumbnail" style="border-radius: 100%;">
                        <a class="nav-link text-primary" href="#" id="logout" onclick="Logout()">Logout</a>
                    </li>
                </ul>
            </div>        
        </nav>
    </div>
    <div class="container-fluid" style="margin-top: 150px;">
        <div class="row" >
            <div class="col-12 tabpanel">
                <ul class="nav nav-tabs bg-dark">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#usuarios">Listado Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#autos">Listado Autos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#altaAuto">Alta Auto</a>
                    </li>
                </ul>
                <div class="tab-content border" style="min-height:800px;">
                    <div id="usuarios" class="container tab-pane active"><br>
                    </div>
                    <div id="autos" class="container tab-pane fade"><br>
                    </div>
                    <div id="altaAuto" class="container tab-pane fade"><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
{% endblock %}