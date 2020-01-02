<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
        data-accordion="false">
        <li class="nav-item">
            <a href="<?=$nav["info"]?>" class="nav-link <?= $active["info"] ?>">
                <i class="nav-icon fas fa-info"></i>
                <p>Info</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?=$nav["bots"]?>" class="nav-link <?= $active["bots"] ?>">
                <i class="nav-icon fas fa-robot"></i>
                <p>Bots</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="#HERE PHP $nav['createuser']#" class="nav-link #HERE PHP $active['createuser']#">
                <i class="fas fa-user-plus"></i>
                <p>Create User</p>
            </a>
        </li>
        <li class="nav-item has-treeview <?php if (!empty($active["settings_s"])) echo "menu-open" ?>">
            <a href="#" class="nav-link <?= $active["settings_s"] ?>">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                    Settings
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?=$nav["settings"]["database"]?>" class="nav-link <?= $active["settings"]["database"] ?>">
                        <i class="fas fa-database nav-icon"></i>
                        <p>Database</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?=$nav["settings"]["bot"]?>" class="nav-link <?= $active["settings"]["bot"] ?>">
                        <i class="fas fa-robot nav-icon"></i>
                        <p>Bot</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?=$nav["settings"]["ssh"]?>" class="nav-link <?= $active["settings"]["ssh"] ?>">
                        <i class="fas fa-signal nav-icon"></i>
                        <p>SSH</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?=$nav["settings"]["other"]?>" class="nav-link <?= $active["settings"]["other"] ?>">
                        <i class="fas fa-ellipsis-h nav-icon"></i>
                        <p>Other</p>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>