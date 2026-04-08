trait HasAuditColumns {
    protected static function bootHasAuditColumns() {
        static::creating(fn ($model) => $model->created_by = auth()->id());
        static::updating(fn ($model) => $model->updated_by = auth()->id());
    }
}