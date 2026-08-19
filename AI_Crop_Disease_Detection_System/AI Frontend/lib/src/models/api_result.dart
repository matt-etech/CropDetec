class ApiResult<T> {
  const ApiResult.success(this.data)
    : errorMessage = null,
      errorCode = null,
      isSuccess = true;

  const ApiResult.failure(this.errorMessage, {this.errorCode})
    : data = null,
      isSuccess = false;

  final T? data;
  final String? errorMessage;
  final String? errorCode;
  final bool isSuccess;
}
