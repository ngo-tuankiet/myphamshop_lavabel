<?php
function renderProductSection($config) {
    $defaults = [
        'sidebarPosition' => 'left', // 'left' hoặc 'right'
        'sidebarTitle' => '',
        'sidebarImage' => '',
        'tabs' => [],
        'products' => [],
        'showViewMore' => true
    ];
    
    $config = array_merge($defaults, $config);
    $isLeftSidebar = $config['sidebarPosition'] === 'left';
    ?>
    
    <div class="mt-5 d-flex bg-white">
        <?php if ($isLeftSidebar): ?>
            <?php renderSidebar($config['sidebarTitle'], $config['sidebarImage']); ?>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="col-10">
            <div class="tab-content">
                <!-- Tab Navigation -->
                <div class="p-3 d-flex justify-content-between align-items-center" 
                     style="background-color: #e0e0e0;">
                    <?php if ($isLeftSidebar): ?>
                        <div class="d-flex gap-4">
                            <?php foreach ($config['tabs'] as $tab): ?>
                                <a href="#" class="text-decoration-none text-secondary"><?= $tab ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($config['showViewMore']): ?>
                            <a href="#" class="text-danger text-decoration-none fw-bold">XEM THÊM</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($config['showViewMore']): ?>
                            <a href="#" class="text-danger text-decoration-none fw-bold">XEM THÊM</a>
                        <?php endif; ?>
                        <div class="d-flex gap-4">
                            <?php foreach ($config['tabs'] as $tab): ?>
                                <a href="#" class="text-decoration-none text-secondary"><?= $tab ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Products Grid -->
                <div class="row p-2">
                    <?php foreach ($config['products'] as $product): ?>
                        <div class="col-3 w-25">
                            <?php renderProductCard($product); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!$isLeftSidebar): ?>
            <?php renderSidebar($config['sidebarTitle'], $config['sidebarImage']); ?>
        <?php endif; ?>
    </div>
    <?php
}

function renderSidebar($title, $image) {
    ?>
    <div class="col-2 d-flex flex-column">
        <div class="bg-dark p-3 text-white fw-bold text-center"><?= $title ?></div>
        <div class="flex-grow-1 d-flex align-items-center">
            <img src="<?= $image ?>" class="img-fluid w-100 h-100" style="object-fit: cover;" />
        </div>
    </div>
    <?php
}
?>