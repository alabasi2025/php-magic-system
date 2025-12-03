/**
 * Task API Client
 * مكتبة JavaScript للتكامل السهل مع Task API
 * 
 * @version 1.0.0
 * @author Manus AI Assistant
 * @license MIT
 */

class TaskAPIClient {
    /**
     * Constructor
     * @param {Object} options - خيارات التكوين
     * @param {string} options.baseUrl - الرابط الأساسي للـ API
     * @param {number} options.timeout - وقت الانتظار بالميلي ثانية (افتراضي: 30000)
     * @param {boolean} options.cache - تفعيل التخزين المؤقت (افتراضي: true)
     * @param {number} options.cacheTTL - مدة التخزين المؤقت بالميلي ثانية (افتراضي: 60000)
     */
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || 'https://php-magic-system-main-4kqldr.laravel.cloud';
        this.timeout = options.timeout || 30000;
        this.cacheEnabled = options.cache !== false;
        this.cacheTTL = options.cacheTTL || 60000;
        this.cache = new Map();
    }

    /**
     * الحصول على تفاصيل مهمة
     * @param {string} taskId - معرّف المهمة
     * @param {Object} options - خيارات إضافية
     * @param {boolean} options.useCache - استخدام التخزين المؤقت (افتراضي: true)
     * @returns {Promise<Object>} - بيانات المهمة
     */
    async getTask(taskId, options = {}) {
        const useCache = options.useCache !== false && this.cacheEnabled;

        // التحقق من الـ cache
        if (useCache && this.cache.has(taskId)) {
            const cached = this.cache.get(taskId);
            if (Date.now() - cached.timestamp < this.cacheTTL) {
                return cached.data;
            }
        }

        const url = `${this.baseUrl}/developer/ai/task/${taskId}`;

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.timeout);

            const response = await fetch(url, {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                }
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${await this._getErrorMessage(response)}`);
            }

            const data = await response.json();

            // حفظ في الـ cache
            if (useCache) {
                this.cache.set(taskId, {
                    data: data,
                    timestamp: Date.now()
                });
            }

            return data;

        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('انتهت مهلة الطلب');
            }
            throw error;
        }
    }

    /**
     * الحصول على عدة مهام دفعة واحدة
     * @param {string[]} taskIds - قائمة معرّفات المهام
     * @returns {Promise<Object[]>} - قائمة بيانات المهام
     */
    async getMultipleTasks(taskIds) {
        const promises = taskIds.map(id => 
            this.getTask(id).catch(error => ({
                error: true,
                taskId: id,
                message: error.message
            }))
        );

        return Promise.all(promises);
    }

    /**
     * مراقبة حالة المهمة حتى تكتمل
     * @param {string} taskId - معرّف المهمة
     * @param {Object} options - خيارات المراقبة
     * @param {number} options.maxAttempts - أقصى عدد محاولات (افتراضي: 60)
     * @param {number} options.interval - الفترة بين المحاولات بالميلي ثانية (افتراضي: 2000)
     * @param {Function} options.onProgress - دالة يتم استدعاؤها عند كل محاولة
     * @returns {Promise<Object>} - بيانات المهمة المكتملة
     */
    async waitForCompletion(taskId, options = {}) {
        const maxAttempts = options.maxAttempts || 60;
        const interval = options.interval || 2000;
        const onProgress = options.onProgress || (() => {});

        for (let attempt = 1; attempt <= maxAttempts; attempt++) {
            const task = await this.getTask(taskId, { useCache: false });

            onProgress({
                attempt,
                maxAttempts,
                status: task.status,
                task
            });

            if (task.status === 'completed') {
                return task;
            }

            if (task.status === 'failed') {
                throw new Error(`فشلت المهمة: ${task.error || 'خطأ غير معروف'}`);
            }

            if (attempt < maxAttempts) {
                await this._sleep(interval);
            }
        }

        throw new Error('لم تكتمل المهمة في الوقت المحدد');
    }

    /**
     * الحصول على سجل المحادثة
     * @param {string} taskId - معرّف المهمة
     * @returns {Promise<Object[]>} - قائمة الرسائل
     */
    async getConversation(taskId) {
        const task = await this.getTask(taskId);
        return task.output || [];
    }

    /**
     * الحصول على إحصائيات المهمة
     * @param {string} taskId - معرّف المهمة
     * @returns {Promise<Object>} - إحصائيات المهمة
     */
    async getTaskStats(taskId) {
        const task = await this.getTask(taskId);

        return {
            id: task.id,
            status: task.status,
            model: task.model,
            credits: task.credit_usage || 0,
            messageCount: (task.output || []).length,
            userMessages: (task.output || []).filter(m => m.role === 'user').length,
            assistantMessages: (task.output || []).filter(m => m.role === 'assistant').length,
            createdAt: new Date(parseInt(task.created_at) * 1000),
            updatedAt: new Date(parseInt(task.updated_at) * 1000),
            duration: parseInt(task.updated_at) - parseInt(task.created_at),
            taskUrl: task.metadata?.task_url || null
        };
    }

    /**
     * تصدير المحادثة إلى نص
     * @param {string} taskId - معرّف المهمة
     * @param {string} format - صيغة التصدير ('text', 'markdown', 'json')
     * @returns {Promise<string>} - المحادثة بالصيغة المطلوبة
     */
    async exportConversation(taskId, format = 'text') {
        const task = await this.getTask(taskId);
        const messages = task.output || [];

        switch (format) {
            case 'markdown':
                return this._exportToMarkdown(task, messages);
            case 'json':
                return JSON.stringify(messages, null, 2);
            case 'text':
            default:
                return this._exportToText(task, messages);
        }
    }

    /**
     * مسح الـ cache
     * @param {string} taskId - معرّف المهمة (اختياري، إذا لم يُحدد يتم مسح الكل)
     */
    clearCache(taskId = null) {
        if (taskId) {
            this.cache.delete(taskId);
        } else {
            this.cache.clear();
        }
    }

    /**
     * الحصول على حجم الـ cache
     * @returns {number} - عدد المهام المخزنة
     */
    getCacheSize() {
        return this.cache.size;
    }

    // ==================== Private Methods ====================

    async _getErrorMessage(response) {
        try {
            const data = await response.json();
            return data.error || data.message || response.statusText;
        } catch {
            return response.statusText;
        }
    }

    _sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    _exportToText(task, messages) {
        let text = `سجل المحادثة - ${task.id}\n`;
        text += `التاريخ: ${new Date(task.created_at * 1000).toLocaleString('ar-SA')}\n`;
        text += `النموذج: ${task.model}\n`;
        text += `الحالة: ${task.status}\n`;
        text += `\n${'='.repeat(50)}\n\n`;

        messages.forEach((message, index) => {
            const role = message.role === 'user' ? 'المستخدم' : 'المساعد';
            const text_content = message.content[0]?.text || '';
            text += `[${role}]\n${text_content}\n\n`;
        });

        text += `${'='.repeat(50)}\n`;
        text += `Credits المستخدمة: ${task.credit_usage}\n`;

        return text;
    }

    _exportToMarkdown(task, messages) {
        let md = `# سجل المحادثة - ${task.id}\n\n`;
        md += `**التاريخ:** ${new Date(task.created_at * 1000).toLocaleString('ar-SA')}\n`;
        md += `**النموذج:** ${task.model}\n`;
        md += `**الحالة:** ${task.status}\n\n`;
        md += `---\n\n`;

        messages.forEach((message, index) => {
            const role = message.role === 'user' ? '👤 المستخدم' : '🤖 المساعد';
            const text = message.content[0]?.text || '';
            md += `## ${role}\n\n${text}\n\n`;
        });

        md += `---\n\n`;
        md += `**Credits المستخدمة:** ${task.credit_usage}\n`;

        return md;
    }
}

// ==================== Helper Functions ====================

/**
 * إنشاء instance جديد من TaskAPIClient
 * @param {Object} options - خيارات التكوين
 * @returns {TaskAPIClient}
 */
function createTaskClient(options = {}) {
    return new TaskAPIClient(options);
}

/**
 * دالة مساعدة سريعة للحصول على مهمة
 * @param {string} taskId - معرّف المهمة
 * @returns {Promise<Object>}
 */
async function getTask(taskId) {
    const client = new TaskAPIClient();
    return client.getTask(taskId);
}

// ==================== Export ====================

// للاستخدام في Node.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        TaskAPIClient,
        createTaskClient,
        getTask
    };
}

// للاستخدام في المتصفح
if (typeof window !== 'undefined') {
    window.TaskAPIClient = TaskAPIClient;
    window.createTaskClient = createTaskClient;
    window.getTask = getTask;
}
