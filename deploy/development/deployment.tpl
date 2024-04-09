apiVersion: apps/v1
kind: Deployment
metadata:
  name: ${KUBE_NAMESPACE}
  namespace: ${KUBE_NAMESPACE}
  labels:
    app: ${KUBE_NAMESPACE}
spec:
  replicas: 1
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxUnavailable: 0
      maxSurge: 100%
  selector:
    matchLabels:
      app: ${KUBE_NAMESPACE}
  template:
    metadata:
      labels:
        app: ${KUBE_NAMESPACE}
    spec:
      volumes:
        - name: uploads
          emptyDir: { }
      terminationGracePeriodSeconds: 35
      serviceAccountName: ${KUBE_NAMESPACE}-service
      containers:
      - name: nginx
        image: ${ECR_URL}:${IMAGE_TAG_NGINX}
        ports:
          - containerPort: 8080
        volumeMounts:
          - name: uploads
            mountPath: /var/www/html/public/app/uploads
      - name: fpm
        image: ${ECR_URL}:${IMAGE_TAG_FPM}
        ports:
          - containerPort: 9000
        volumeMounts:
          - name: uploads
            mountPath: /var/www/html/public/app/uploads
        securityContext:
            runAsUser: 82
        env:
          - name: S3_BUCKET_NAME
            valueFrom:
              secretKeyRef:
                name: s3-bucket-output
                key: bucket_name
          - name: CLOUDFRONT_URL
            valueFrom:
              secretKeyRef:
                name: cloudfront-output
                key: cloudfront_url
          - name: CLOUDFRONT_PUBLIC_KEY_OBJECT
            valueFrom:
              secretKeyRef:
                name: cloudfront-output
                key: cloudfront_public_key_ids
          - name: DB_HOST
            valueFrom:
              secretKeyRef:
                name: rds-output
                key: rds_instance_address
          - name: DB_NAME
            valueFrom:
              secretKeyRef:
                name: rds-output
                key: database_name
          - name: DB_USER
            valueFrom:
              secretKeyRef:
                name: rds-output
                key: database_username
          - name: DB_PASSWORD
            valueFrom:
              secretKeyRef:
                name: rds-output
                key: database_password
          - name: OPENSEARCH_URL
            valueFrom:
              secretKeyRef:
                name: central-digital-product-team-opensearch-proxy-url
                key: proxy_url
        envFrom:
          - configMapRef:
              name: ${KUBE_NAMESPACE}
          - secretRef:
              name: ${KUBE_NAMESPACE}-secrets
