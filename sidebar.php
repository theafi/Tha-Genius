<nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item <?php if ($_GET['section'] === 'Inicio' || !(isset($_GET['section']))) { echo "active"; }?>">
                        <a class="nav-link" href="index.php?section=Inicio">Inicio </a>
                        </li>
                    </ul>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                        <a class="nav-link" href="#">Administrar</a>
                            <ul class="nav nav-admin flex-column">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?section=usuarios">   Usuarios</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?section=dominios">   Dominios</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" href="index.php?section=forwardings">    Redirecciones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" class="" href="index.php?section=transport"> Transporte</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                        </li>
                    </ul>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                        </li>
                    </ul>
                </nav>