<?php $__env->startSection('container'); ?>
<div class="mx-auto max-w-7xl px-4 py-8">
    
    
    <?php echo $__env->make('dashboard.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(auth()->user()->role === 'admin'): ?>
        
        
        
        <?php echo $__env->make('dashboard.admin_stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('dashboard.admin_charts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('dashboard.admin_tables', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('dashboard.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php else: ?>
        
        
        <?php echo $__env->make('dashboard.user_view', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> 
    <?php endif; ?>

</div>


<?php echo $__env->make('dashboard.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\simaset_fix\resources\views/home.blade.php ENDPATH**/ ?>