<!-- Users List -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Users List (<?php echo count($users); ?>)</h2>

    <?php if (empty($users)): ?>
        <p class="text-gray-500 text-center py-8">No users found. Add your first user!</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3 hidden sm:table-cell">Gender</th>
                        <th class="px-3 py-3 hidden md:table-cell">Country</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-3 py-4 font-medium text-gray-900">
                                <?php echo htmlspecialchars($user['name']); ?>
                            </td>
                            <td class="px-3 py-4 text-gray-700">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td class="px-3 py-4 text-gray-700 hidden sm:table-cell">
                                <?php echo htmlspecialchars($user['gender'] ?: '-'); ?>
                            </td>
                            <td class="px-3 py-4 text-gray-700 hidden md:table-cell">
                                <?php echo htmlspecialchars($user['country'] ?: '-'); ?>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex gap-2">
                                    <a href="?edit=<?php echo $user['id']; ?>"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>