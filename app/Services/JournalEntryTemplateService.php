<?php

namespace App\Services;

use App\Models\JournalEntryTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * خدمة إدارة قوالب القيود اليومية الذكية.
 */
class JournalEntryTemplateService
{
    /**
     * @var JournalEntryTemplate
     */
    protected JournalEntryTemplate $templateModel;

    /**
     * تهيئة الخدمة.
     *
     * @param JournalEntryTemplate $templateModel
     */
    public function __construct(JournalEntryTemplate $templateModel)
    {
        $this->templateModel = $templateModel;
    }

    /**
     * جلب جميع القوالب النشطة.
     *
     * @return Collection
     */
    public function getAllTemplates(): Collection
    {
        // جلب القوالب الخاصة بالمستخدم الحالي أو القوالب العامة إذا كان هناك منطق لذلك
        // هنا نفترض أن القوالب خاصة بالمستخدم الذي أنشأها
        return $this->templateModel->where('user_id', Auth::id())->get();
    }

    /**
     * إنشاء قالب جديد.
     *
     * @param array $data
     * @return JournalEntryTemplate
     */
    public function createTemplate(array $data): JournalEntryTemplate
    {
        // إضافة user_id للمستخدم الحالي
        $data['user_id'] = Auth::id();
        // التأكد من أن template_data هو JSON صالح أو مصفوفة
        if (isset($data['template_data']) && is_string($data['template_data'])) {
            $data['template_data'] = json_decode($data['template_data'], true);
        }

        return $this->templateModel->create($data);
    }

    /**
     * تحديث قالب موجود.
     *
     * @param JournalEntryTemplate $template
     * @param array $data
     * @return JournalEntryTemplate
     */
    public function updateTemplate(JournalEntryTemplate $template, array $data): JournalEntryTemplate
    {
        // التأكد من أن template_data هو JSON صالح أو مصفوفة
        if (isset($data['template_data']) && is_string($data['template_data'])) {
            $data['template_data'] = json_decode($data['template_data'], true);
        }

        $template->update($data);
        return $template;
    }

    /**
     * حذف قالب (حذف ناعم).
     *
     * @param JournalEntryTemplate $template
     * @return bool|null
     */
    public function deleteTemplate(JournalEntryTemplate $template): ?bool
    {
        return $template->delete();
    }

    /**
     * جلب بيانات القالب لتطبيقها على قيد جديد.
     *
     * @param JournalEntryTemplate $template
     * @return array
     */
    public function applyTemplate(JournalEntryTemplate $template): array
    {
        // إرجاع بيانات القيد المخزنة في القالب
        return $template->template_data;
    }

    /**
     * حفظ بيانات قيد موجود كقالب جديد.
     *
     * @param array $journalEntryData بيانات القيد المراد حفظه كقالب
     * @param string $name اسم القالب
     * @param string|null $description وصف القالب
     * @return JournalEntryTemplate
     */
    public function saveAsTemplate(array $journalEntryData, string $name, ?string $description = null): JournalEntryTemplate
    {
        $data = [
            'name' => $name,
            'description' => $description,
            'template_data' => $journalEntryData,
            'user_id' => Auth::id(),
            'is_active' => true,
        ];

        return $this->templateModel->create($data);
    }
}
