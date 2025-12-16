<!-- User Form -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        <?php echo $editUser ? 'Edit User' : 'Add New User'; ?>
    </h2>

    <form method="POST" class="space-y-4">
        <input type="hidden" name="action" value="<?php echo $editUser ? 'edit' : 'add'; ?>">
        <?php if ($editUser): ?>
            <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
        <?php endif; ?>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input type="text" id="name" name="name" required
                   value="<?php echo $editUser ? htmlspecialchars($editUser['name']) : ''; ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo $editUser ? htmlspecialchars($editUser['email']) : ''; ?>"
                   placeholder="username@webmail.npru.ac.th"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select id="gender" name="gender"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select Gender</option>
                <option value="Male" <?php echo ($editUser && $editUser['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($editUser && $editUser['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($editUser && $editUser['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div>
            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
            <input type="text" id="country" name="country"
                   value="<?php echo $editUser ? htmlspecialchars($editUser['country']) : ''; ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <?php echo $editUser ? 'Update User' : 'Add User'; ?>
            </button>

            <?php if ($editUser): ?>
                <a href="index.php"
                   class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 text-center">
                    Cancel
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>