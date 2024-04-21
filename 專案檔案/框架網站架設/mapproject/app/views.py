from django.shortcuts import render

# Create your views here.

from django.http import HttpResponse
def sayhello(request):
    return HttpResponse("Hello World!")

def hello2(request, username):
    return HttpResponse(f"Hello {username}")